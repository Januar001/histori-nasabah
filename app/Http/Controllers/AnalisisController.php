<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Helpers\QualityHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalisisController extends Controller
{
    public function kolektibilitasMurni(Request $request)
    {
        $selectedDate = $request->input('tanggal', Nasabah::max('tanggal_laporan'));
        $compareDate = Carbon::parse($selectedDate)->subMonth()->format('Y-m-d');

        $availableDates = Nasabah::select('tanggal_laporan')
            ->distinct()
            ->orderBy('tanggal_laporan', 'desc')
            ->pluck('tanggal_laporan');
        
        if(!$selectedDate) {
            return view('analisis.kolektibilitas-murni', [
                'availableDates' => $availableDates,
                'selectedDate' => null,
                'results' => [],
            ]);
        }

        $results = [];

        for ($kualitas = 1; $kualitas <= 5; $kualitas++) {
            $nasabahBulanIni = Nasabah::where('tanggal_laporan', $selectedDate)
                ->where('kualitas', $kualitas)
                ->pluck('nocif');

            $nasabahBulanLalu = Nasabah::where('tanggal_laporan', $compareDate)
                ->where('kualitas', $kualitas)
                ->pluck('nocif');

            $kolekMurni = $nasabahBulanIni->intersect($nasabahBulanLalu);
            
            $masukDariKolLain = $nasabahBulanIni->diff($kolekMurni);
            
            $potensiMasukBulanDepan = $this->getPotensiMasuk($selectedDate, $kualitas);

            $results[$kualitas] = [
                'total_nasabah' => $nasabahBulanIni->count(),
                'kolek_murni_count' => $kolekMurni->count(),
                'masuk_count' => $masukDariKolLain->count(),
                'potensi_masuk_count' => $potensiMasukBulanDepan->count(),
                'nasabah_masuk' => Nasabah::where('tanggal_laporan', $selectedDate)->whereIn('nocif', $masukDariKolLain)->get(),
                'nasabah_potensi_masuk' => $potensiMasukBulanDepan,
            ];
        }

        return view('analisis.kolektibilitas-murni', compact('results', 'availableDates', 'selectedDate'));
    }

    private function getPotensiMasuk($currentDate, $targetKualitas)
    {
        if ($targetKualitas == 1) return collect(); 

        $previousKualitas = $targetKualitas - 1;

        $potensi = Nasabah::where('tanggal_laporan', $currentDate)
            ->where('kualitas', $previousKualitas)
            ->where(function ($query) {
                // Logika sederhana: tunggakan pokok atau bunga di atas 0
                $query->where('xtungpok', '>', 0)
                      ->orWhere('xtungbu', '>', 0);
            })
            ->get();
            
        return $potensi;
    }
}