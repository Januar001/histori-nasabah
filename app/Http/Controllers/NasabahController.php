<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\KolektibilitasHistory;
use App\Models\JanjiBayar;
use App\Models\Petugas;
use Illuminate\Http\Request;
use App\Helpers\QualityHelper;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $query = Nasabah::with('petugas');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('namadb', 'like', "%{$search}%")
                  ->orWhere('nocif', 'like', "%{$search}%")
                  ->orWhere('rekening', 'like', "%{$search}%");
            });
        }

        // Kualitas filter
        if ($request->filled('kualitas')) {
            $query->where('kualitas', $request->kualitas);
        }

        // Petugas filter
        if ($request->filled('petugas_id')) {
            if ($request->petugas_id === 'null') {
                $query->whereNull('petugas_id');
            } else {
                $query->where('petugas_id', $request->petugas_id);
            }
        }

        $nasabahs = $query->orderBy('namadb')->paginate(20);
        $petugas = Petugas::aktif()->get();
        $qualityOptions = QualityHelper::getAllQualityOptions();

        return view('nasabah.index', compact('nasabahs', 'petugas', 'qualityOptions'));
    }

    public function show($id)
    {
        $nasabah = Nasabah::with(['historyKolektibilitas', 'janjiBayar', 'petugas'])->findOrFail($id);
        $petugasList = Petugas::aktif()->get();
        
        return view('nasabah.show', compact('nasabah', 'petugasList'));
    }
}