<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KolektibilitasHistory;
use App\Helpers\QualityHelper;
use Carbon\Carbon;

class AnalisisController extends Controller
{
    public function pergerakanKol(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $results = [];
        for ($i = 1; $i <= 5; $i++) {
            $results[$i] = [
                'masuk_membaik' => $this->getPergerakanData($startDate, $endDate, $i, 'membaik'),
                'masuk_memburuk' => $this->getPergerakanData($startDate, $endDate, $i, 'memburuk'),
                'keluar_membaik' => $this->getPergerakanData($startDate, $endDate, $i, 'keluar'),
            ];
        }

        return view('analisis.pergerakan-kol', compact('results', 'startDate', 'endDate'));
    }

    private function getPergerakanData($startDate, $endDate, $kualitas, $jenis)
    {
        $query = KolektibilitasHistory::with('nasabah')
            ->whereBetween('tanggal_perubahan', [$startDate, $endDate]);
        
        switch ($jenis) {
            case 'membaik': // Masuk ke KOL $kualitas karena membaik
                $query->where('kolektibilitas_sesudah', $kualitas)
                      ->whereColumn('kolektibilitas_sesudah', '<', 'kolektibilitas_sebelum');
                break;
            case 'memburuk': // Masuk ke KOL $kualitas karena memburuk
                $query->where('kolektibilitas_sesudah', $kualitas)
                      ->whereColumn('kolektibilitas_sesudah', '>', 'kolektibilitas_sebelum');
                break;
            case 'keluar': // Keluar dari KOL $kualitas karena membaik
                $query->where('kolektibilitas_sebelum', $kualitas)
                      ->whereColumn('kolektibilitas_sesudah', '<', 'kolektibilitas_sebelum');
                break;
        }

        return $query->get();
    }
}