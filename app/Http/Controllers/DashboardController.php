<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\QualityHelper;
use App\Models\Nasabah;
use App\Models\Petugas;
use App\Models\JanjiBayar;
use App\Models\KolektibilitasHistory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
     public function index(Request $request)
    {
        // === Data Statistik Utama ===
        $totalNasabah = Nasabah::count();
        $totalBakiDebet = Nasabah::sum('bakidebet');
        $petugasAktif = Petugas::where('status_aktif', true)->count();
        
        // === Statistik Janji Bayar (disesuaikan dengan status baru) ===
        $janjiBayarAktif = JanjiBayar::where('status', 'pending')->count();
        $janjiBayarDitepatiBulanIni = JanjiBayar::where('status', 'sukses')->whereMonth('tanggal_janji', now()->month)->whereYear('tanggal_janji', now()->year)->count();
        $janjiBayarIngkarBulanIni = JanjiBayar::where('status', 'gagal')->whereMonth('tanggal_janji', now()->month)->whereYear('tanggal_janji', now()->year)->count();

        // === Analisis Tren Perubahan (Bulan Ini vs Bulan Lalu) ===
        $membaikBulanIni = $this->getPerubahanCount(now()->startOfMonth(), now()->endOfMonth(), 'membaik');
        $memburukBulanIni = $this->getPerubahanCount(now()->startOfMonth(), now()->endOfMonth(), 'memburuk');
        $membaikBulanLalu = $this->getPerubahanCount(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth(), 'membaik');
        $memburukBulanLalu = $this->getPerubahanCount(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth(), 'memburuk');
        
        $trenPerubahan = [
            'membaik' => $this->calculateTrend($membaikBulanIni, $membaikBulanLalu),
            'memburuk' => $this->calculateTrend($memburukBulanIni, $memburukBulanLalu),
            'total_membaik' => $membaikBulanIni,
            'total_memburuk' => $memburukBulanIni
        ];

        // === Kinerja Petugas Bulan Ini ===
        $performance = DB::table('kolektibilitas_history')
            ->join('petugas', 'kolektibilitas_history.petugas_id', '=', 'petugas.id')
            ->whereNotNull('kolektibilitas_history.petugas_id')
            ->whereMonth('kolektibilitas_history.created_at', now()->month)
            ->whereYear('kolektibilitas_history.created_at', now()->year)
            ->select(
                'petugas.nama_petugas as petugas', 'petugas.divisi',
                DB::raw('COUNT(*) as total_perubahan'),
                DB::raw('SUM(CASE WHEN kolektibilitas_sesudah < kolektibilitas_sebelum THEN 1 ELSE 0 END) as berhasil_memperbaiki')
            )
            ->groupBy('petugas.id', 'petugas.nama_petugas', 'petugas.divisi')
            ->orderBy('berhasil_memperbaiki', 'desc')->limit(5)->get();

        // === Distribusi Kolektibilitas (Charts) ===
        $distribusiPerJumlah = Nasabah::select('kualitas', DB::raw('COUNT(*) as total'))->groupBy('kualitas')->orderBy('kualitas')->get()->mapWithKeys(fn($item) => [QualityHelper::getQualityLabel($item->kualitas) => $item->total]);
        $distribusiPerBakiDebet = Nasabah::select('kualitas', DB::raw('SUM(bakidebet) as total'))->groupBy('kualitas')->orderBy('kualitas')->get()->mapWithKeys(fn($item) => [QualityHelper::getQualityLabel($item->kualitas) => $item->total]);
            
        // === Aktivitas Perubahan Terbaru ===
        $aktivitasPerubahan = KolektibilitasHistory::with(['nasabah', 'petugasRelasi'])->orderBy('created_at', 'desc')->limit(5)->get();

        // === Daftar Janji Bayar dengan Query yang Sudah Diperbaiki ===
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $janjiBayarList = JanjiBayar::where('status', 'pending') // DIUBAH DARI 'dijadwalkan'
            ->whereBetween('tanggal_janji', [$startDate, $endDate])
            ->with('nasabah:id,namadb,nocif') // Relasi ke petugas dihapus
            ->orderBy('tanggal_janji', 'asc')
            ->get();
        
        return view('dashboard', compact(
            'totalNasabah', 'totalBakiDebet', 'petugasAktif', 'janjiBayarAktif', 'janjiBayarDitepatiBulanIni', 
            'janjiBayarIngkarBulanIni', 'trenPerubahan', 'performance', 'distribusiPerJumlah', 
            'distribusiPerBakiDebet', 'aktivitasPerubahan', 'janjiBayarList', 'startDate', 'endDate'
        ));
    }
    
    private function getPerubahanCount(Carbon $start, Carbon $end, string $type): int
    {
        $query = KolektibilitasHistory::whereBetween('tanggal_perubahan', [$start, $end]);
        if ($type === 'membaik') {
            $query->whereColumn('kolektibilitas_sesudah', '<', 'kolektibilitas_sebelum');
        } else {
            $query->whereColumn('kolektibilitas_sesudah', '>', 'kolektibilitas_sebelum');
        }
        return $query->count();
    }
    
    private function calculateTrend(int $current, int $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }
        return (($current - $previous) / $previous) * 100;
    }
}