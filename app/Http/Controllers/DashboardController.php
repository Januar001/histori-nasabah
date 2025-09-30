<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\JanjiBayar;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\KolektibilitasHistory;

class DashboardController extends Controller
{
    public function index()
    {
        $totalNasabah = Nasabah::count();
        $totalPerubahan = KolektibilitasHistory::whereMonth('created_at', now()->month)->count();
        $janjiHariIni = JanjiBayar::whereDate('tanggal_janji', today())->count();
        
        // Performance petugas
        $performance = DB::table('kolektibilitas_history')
            ->select('petugas', DB::raw('COUNT(*) as total_perubahan'))
            ->whereMonth('created_at', now()->month)
            ->groupBy('petugas')
            ->get();

        $recentActivities = KolektibilitasHistory::with('nasabah')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalNasabah', 
            'totalPerubahan', 
            'janjiHariIni',
            'performance',
            'recentActivities'
        ));
    }
}
