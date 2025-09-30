<?php

namespace App\Http\Controllers;

use App\Models\JanjiBayar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JanjiBayarController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'tanggal_janji' => 'required|date',
            'nominal_janji' => 'required|numeric',
            'keterangan' => 'required'
        ]);

        JanjiBayar::create([
            'nasabah_id' => $request->nasabah_id,
            'tanggal_janji' => $request->tanggal_janji,
            'nominal_janji' => $request->nominal_janji,
            'keterangan' => $request->keterangan,
            'created_by' => Auth::user()->name,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Janji bayar berhasil disimpan!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,sukses,gagal'
        ]);

        $janjiBayar = JanjiBayar::findOrFail($id);
        $janjiBayar->update(['status' => $request->status]);

        return back()->with('success', 'Status janji bayar berhasil diupdate!');
    }
}
