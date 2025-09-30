<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Petugas;
use Illuminate\Http\Request;
use App\Models\KolektibilitasHistory;

class PenangananController extends Controller
{
    public function assignPenanganan(Request $request, $id)
    {
        $request->validate([
            'petugas_id' => 'required|exists:petugas,id',
            'catatan_penanganan' => 'nullable|string'
        ]);

        $nasabah = Nasabah::findOrFail($id);
        $petugas = Petugas::findOrFail($request->petugas_id);
        
        $nasabah->update([
            'petugas_id' => $request->petugas_id,
            'tanggal_ditangani' => now(),
            'catatan_penanganan' => $request->catatan_penanganan
        ]);

        KolektibilitasHistory::where('nasabah_id', $id)
            ->whereNull('petugas_id')
            ->update([
                'petugas_id' => $request->petugas_id
            ]);

        return back()->with('success', "Nasabah berhasil diassign ke {$petugas->nama_petugas}!");
    }

    public function updateCatatan(Request $request, $id)
    {
        $request->validate([
            'catatan_penanganan' => 'required|string'
        ]);

        $nasabah = Nasabah::findOrFail($id);
        $nasabah->update([
            'catatan_penanganan' => $request->catatan_penanganan
        ]);

        return back()->with('success', 'Catatan penanganan berhasil diupdate!');
    }
}