<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function index()
    {
        $petugas = Petugas::withCount(['nasabahs', 'historyKolektibilitas'])
            ->orderBy('divisi')
            ->orderBy('nama_petugas')
            ->get();

        $stats = [
            'total' => Petugas::count(),
            'ao' => Petugas::ao()->aktif()->count(),
            'remedial' => Petugas::remedial()->aktif()->count(),
            'special' => Petugas::special()->aktif()->count(),
        ];

        return view('petugas.index', compact('petugas', 'stats'));
    }

    public function create()
    {
        return view('petugas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_petugas' => 'required|unique:petugas|max:20',
            'nama_petugas' => 'required|max:255',
            'divisi' => 'required|in:AO,Remedial,Special',
            'email' => 'nullable|email',
            'telepon' => 'nullable|max:20'
        ]);

        Petugas::create($request->all());

        return redirect()->route('petugas.index')
            ->with('success', 'Petugas berhasil ditambahkan!');
    }

    public function show($id)
    {
        $petugas = Petugas::with(['nasabahs', 'historyKolektibilitas'])
            ->findOrFail($id);

        $performance = $this->calculatePerformance($petugas);

        return view('petugas.show', compact('petugas', 'performance'));
    }

    public function edit($id)
    {
        $petugas = Petugas::findOrFail($id);
        return view('petugas.edit', compact('petugas'));
    }

    public function update(Request $request, $id)
    {
        $petugas = Petugas::findOrFail($id);

        $request->validate([
            'kode_petugas' => 'required|max:20|unique:petugas,kode_petugas,' . $id,
            'nama_petugas' => 'required|max:255',
            'divisi' => 'required|in:AO,Remedial,Special',
            'email' => 'nullable|email',
            'telepon' => 'nullable|max:20',
            'status_aktif' => 'boolean'
        ]);

        $petugas->update($request->all());

        return redirect()->route('petugas.index')
            ->with('success', 'Data petugas berhasil diupdate!');
    }

    public function destroy($id)
    {
        $petugas = Petugas::findOrFail($id);

        if ($petugas->nasabahs()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus petugas yang masih menangani nasabah!');
        }

        $petugas->delete();

        return redirect()->route('petugas.index')
            ->with('success', 'Petugas berhasil dihapus!');
    }

    private function calculatePerformance($petugas)
    {
        $history = $petugas->historyKolektibilitas()
            ->whereMonth('created_at', now()->month)
            ->get();

        $totalPerubahan = $history->count();
        $berhasilMemperbaiki = $history->filter(function ($item) {
            return $item->kolektibilitas_sesudah < $item->kolektibilitas_sebelum;
        })->count();

        $successRate = $totalPerubahan > 0 ? ($berhasilMemperbaiki / $totalPerubahan) * 100 : 0;

        return [
            'total_perubahan' => $totalPerubahan,
            'berhasil_memperbaiki' => $berhasilMemperbaiki,
            'success_rate' => round($successRate, 1),
            'total_nasabah' => $petugas->nasabahs()->count()
        ];
    }
}
