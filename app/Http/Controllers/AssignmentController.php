<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Models\Petugas;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $petugasList = Petugas::where('status_aktif', true)->orderBy('nama_petugas')->get();
        
        $query = Nasabah::with('petugas');

        if ($request->filled('kualitas')) {
            $query->where('kualitas', $request->kualitas);
        }
        
        if ($request->filled('nama_nasabah')) {
            $query->where('namadb', 'like', '%' . $request->nama_nasabah . '%');
        }

        if ($request->filled('petugas_id')) {
            if ($request->petugas_id == 'belum_ditugaskan') {
                $query->whereNull('petugas_id');
            } else {
                $query->where('petugas_id', $request->petugas_id);
            }
        }

        $nasabahs = $query->orderBy('kualitas')->orderBy('namadb')->paginate(50);

        return view('assignment.index', compact('nasabahs', 'petugasList'));
    }

    public function assignBulk(Request $request)
    {
        $request->validate([
            'nasabah_ids' => 'required|array|min:1',
            'petugas_id_bulk' => 'required|exists:petugas,id'
        ]);

        Nasabah::whereIn('id', $request->nasabah_ids)
            ->update(['petugas_id' => $request->petugas_id_bulk]);

        return back()->with('success', 'Berhasil menugaskan ' . count($request->nasabah_ids) . ' nasabah.');
    }

    public function assignIndividual(Request $request, Nasabah $nasabah)
    {
        $request->validate([
            'petugas_id_individual' => 'required|exists:petugas,id'
        ]);

        $nasabah->update(['petugas_id' => $request->petugas_id_individual]);
        
        return back()->with('success', 'Berhasil menugaskan nasabah.');
    }
}