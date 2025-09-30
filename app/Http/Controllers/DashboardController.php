<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nasabah;
use App\Models\Petugas;
use App\Models\JanjiBayar;
use Illuminate\Http\Request;
use App\Helpers\QualityHelper;
use Illuminate\Support\Facades\DB;
use App\Models\KolektibilitasHistory;

class DashboardController extends Controller
{
    public function index()
{
    $totalNasabah = Nasabah::count();
    $totalPerubahan = KolektibilitasHistory::whereMonth('created_at', now()->month)->count();
    $janjiHariIni = JanjiBayar::whereDate('tanggal_janji', today())->count();
    $janjiBulanIni = JanjiBayar::whereMonth('tanggal_janji', now()->month)->count();
    
    $performance = DB::table('kolektibilitas_history')
        ->join('petugas', 'kolektibilitas_history.petugas_id', '=', 'petugas.id')
        ->whereNotNull('kolektibilitas_history.petugas_id')
        ->whereMonth('kolektibilitas_history.created_at', now()->month)
        ->select(
            'petugas.nama_petugas as petugas',
            'petugas.divisi',
            DB::raw('COUNT(*) as total_perubahan'),
            DB::raw('SUM(CASE WHEN kolektibilitas_sesudah < kolektibilitas_sebelum THEN 1 ELSE 0 END) as berhasil_memperbaiki')
        )
        ->groupBy('petugas.id', 'petugas.nama_petugas', 'petugas.divisi')
        ->get();

    $recentChanges = KolektibilitasHistory::with(['nasabah', 'petugasRelasi'])
        ->whereNotNull('petugas_id')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    // PERUBAHAN: Ambil data kolektibilitas dari tabel nasabah (bukan dari history perubahan)
    $distribusiKolektibilitas = Nasabah::select(
            'kualitas',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('kualitas')
        ->orderBy('kualitas')
        ->get()
        ->map(function($item) {
            return [
                'kualitas' => QualityHelper::getQualityLabel($item->kualitas),
                'total' => $item->total,
                'kode_kualitas' => $item->kualitas // Tambahkan kode untuk sorting
            ];
        });

    // Jika ingin data perubahan bulan ini (opsional)
    $changesThisMonth = KolektibilitasHistory::whereMonth('created_at', now()->month)
        ->select('kolektibilitas_sesudah', DB::raw('COUNT(*) as total'))
        ->groupBy('kolektibilitas_sesudah')
        ->get()
        ->map(function($item) {
            return [
                'kualitas' => QualityHelper::getQualityLabel($item->kolektibilitas_sesudah),
                'total' => $item->total
            ];
        });

    $monthlyTrend = KolektibilitasHistory::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total_changes')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    $topChanges = KolektibilitasHistory::with(['nasabah', 'petugasRelasi'])
        ->whereMonth('created_at', now()->month)
        ->whereNotNull('petugas_id')
        ->whereIn('kolektibilitas_sesudah', ['1', '5'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $janjiStatus = JanjiBayar::whereMonth('tanggal_janji', now()->month)
        ->select('status', DB::raw('COUNT(*) as total'))
        ->groupBy('status')
        ->get();

    $nasabahDitangani = Nasabah::whereNotNull('petugas_id')->count();
    $petugasAktif = Petugas::aktif()->count();

    $aktivitasPerubahan = KolektibilitasHistory::with(['nasabah', 'petugasRelasi'])
        ->orderBy('created_at', 'desc')
        ->limit(15)
        ->get();

    $statistikPerubahan = DB::table('kolektibilitas_history')
        ->select(
            DB::raw("CASE 
                WHEN kolektibilitas_sesudah < kolektibilitas_sebelum THEN 'Memperbaiki'
                WHEN kolektibilitas_sesudah > kolektibilitas_sebelum THEN 'Memburuk'
                ELSE 'Tidak Berubah'
            END as jenis_perubahan"),
            DB::raw('COUNT(*) as total')
        )
        ->whereMonth('created_at', now()->month)
        ->groupBy('jenis_perubahan')
        ->get();

    return view('dashboard', compact(
        'totalNasabah', 
        'totalPerubahan', 
        'janjiHariIni',
        'janjiBulanIni',
        'performance',
        'recentChanges',
        'changesThisMonth',
        'distribusiKolektibilitas', // GANTI: distribusi kolektibilitas
        'monthlyTrend',
        'topChanges',
        'janjiStatus',
        'nasabahDitangani',
        'petugasAktif',
        'aktivitasPerubahan',
        'statistikPerubahan'
    ));
}
}