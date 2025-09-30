<?php

namespace App\Http\Controllers;

use App\Models\KolektibilitasHistory;
use App\Models\Nasabah;
use App\Models\Petugas;
use Illuminate\Http\Request;
use App\Helpers\QualityHelper;
use Carbon\Carbon;

class KolektibilitasHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = KolektibilitasHistory::with(['nasabah', 'petugasRelasi'])
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            // Default to last 30 days
            $startDate = now()->subDays(30)->startOfDay();
            $endDate = now()->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter by nasabah
        if ($request->has('nasabah_id') && $request->nasabah_id) {
            $query->where('nasabah_id', $request->nasabah_id);
        }

        // Filter by petugas
        if ($request->has('petugas_id') && $request->petugas_id) {
            if ($request->petugas_id === 'null') {
                $query->whereNull('petugas_id');
            } else {
                $query->where('petugas_id', $request->petugas_id);
            }
        }

        // Filter by jenis perubahan
        if ($request->has('jenis_perubahan') && $request->jenis_perubahan) {
            if ($request->jenis_perubahan === 'membaik') {
                $query->whereRaw('kolektibilitas_sesudah < kolektibilitas_sebelum');
            } elseif ($request->jenis_perubahan === 'memburuk') {
                $query->whereRaw('kolektibilitas_sesudah > kolektibilitas_sebelum');
            } elseif ($request->jenis_perubahan === 'tetap') {
                $query->whereRaw('kolektibilitas_sesudah = kolektibilitas_sebelum');
            }
        }

        // Filter by kualitas sebelum
        if ($request->has('kualitas_sebelum') && $request->kualitas_sebelum) {
            $query->where('kolektibilitas_sebelum', $request->kualitas_sebelum);
        }

        // Filter by kualitas sesudah
        if ($request->has('kualitas_sesudah') && $request->kualitas_sesudah) {
            $query->where('kolektibilitas_sesudah', $request->kualitas_sesudah);
        }

        $histories = $query->paginate(50);
        $nasabahs = Nasabah::orderBy('namadb')->get();
        $petugas = Petugas::aktif()->get();
        $qualityOptions = QualityHelper::getAllQualityOptions();

        // Statistics for the filtered results
        $statistics = $this->getStatistics($query);

        return view('kolektibilitas-history.index', compact(
            'histories',
            'nasabahs',
            'petugas',
            'qualityOptions',
            'statistics'
        ));
    }

    private function getStatistics($query)
    {
        $total = $query->count();
        
        $membaik = clone $query;
        $memburuk = clone $query;
        $tetap = clone $query;

        $stats = [
            'total' => $total,
            'membaik' => $membaik->whereRaw('kolektibilitas_sesudah < kolektibilitas_sebelum')->count(),
            'memburuk' => $memburuk->whereRaw('kolektibilitas_sesudah > kolektibilitas_sebelum')->count(),
            'tetap' => $tetap->whereRaw('kolektibilitas_sesudah = kolektibilitas_sebelum')->count(),
        ];

        $stats['persen_membaik'] = $total > 0 ? round(($stats['membaik'] / $total) * 100, 1) : 0;
        $stats['persen_memburuk'] = $total > 0 ? round(($stats['memburuk'] / $total) * 100, 1) : 0;
        $stats['persen_tetap'] = $total > 0 ? round(($stats['tetap'] / $total) * 100, 1) : 0;

        return $stats;
    }

    public function show($id)
    {
        $history = KolektibilitasHistory::with(['nasabah', 'petugasRelasi'])->findOrFail($id);
        
        return view('kolektibilitas-history.show', compact('history'));
    }
}