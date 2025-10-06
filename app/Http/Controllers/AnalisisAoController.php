<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalisisAoController extends Controller
{
    /**
     * Menampilkan halaman analisis kinerja AO dengan filter.
     */
    public function index(Request $request)
    {
        // Validasi input bulan dan tahun, jika tidak ada, gunakan bulan dan tahun saat ini
        $validated = $request->validate([
            'bulan' => 'nullable|integer|between:1,12',
            'tahun' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // Query utama untuk analisis kinerja
        $kinerjaAo = DB::table('kolektibilitas_history')
            ->join('nasabahs', 'kolektibilitas_history.nasabah_id', '=', 'nasabahs.id')
            ->select(
                'nasabahs.namaao',
                // Menghitung jumlah nasabah unik yang ditangani oleh setiap AO
                DB::raw('COUNT(DISTINCT nasabahs.id) as total_nasabah_ditangani'),
                // Menghitung jumlah nasabah yang kinerjanya membaik (Naik Kol)
                DB::raw('COUNT(CASE WHEN kolektibilitas_history.kolektibilitas_sesudah < kolektibilitas_history.kolektibilitas_sebelum THEN 1 END) as perbaikan'),
                // Menghitung jumlah nasabah yang kinerjanya menurun (Turun Kol)
                DB::raw('COUNT(CASE WHEN kolektibilitas_history.kolektibilitas_sesudah > kolektibilitas_history.kolektibilitas_sebelum THEN 1 END) as penurunan'),
                // Menghitung distribusi kolektibilitas berdasarkan kolektibilitas_sesudah
                DB::raw('COUNT(CASE WHEN kolektibilitas_history.kolektibilitas_sesudah = 1 THEN 1 END) as kol_1'),
                DB::raw('COUNT(CASE WHEN kolektibilitas_history.kolektibilitas_sesudah = 2 THEN 1 END) as kol_2'),
                DB::raw('COUNT(CASE WHEN kolektibilitas_history.kolektibilitas_sesudah = 3 THEN 1 END) as kol_3'),
                DB::raw('COUNT(CASE WHEN kolektibilitas_history.kolektibilitas_sesudah = 4 THEN 1 END) as kol_4'),
                DB::raw('COUNT(CASE WHEN kolektibilitas_history.kolektibilitas_sesudah = 5 THEN 1 END) as kol_5')
            )
            // Filter berdasarkan bulan dan tahun yang dipilih
            ->whereMonth('kolektibilitas_history.tanggal_perubahan', $bulan)
            ->whereYear('kolektibilitas_history.tanggal_perubahan', $tahun)
            // Mengabaikan data dimana namaao tidak ada (null atau kosong)
            ->whereNotNull('nasabahs.namaao')
            ->where('nasabahs.namaao', '!=', '')
            // Mengelompokkan hasil berdasarkan nama AO
            ->groupBy('nasabahs.namaao')
            // Mengurutkan berdasarkan nama AO
            ->orderBy('nasabahs.namaao')
            ->get();
            
        // Mengirim data ke view
        return view('analisis.ao', [
            'kinerjaAo' => $kinerjaAo,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }
}