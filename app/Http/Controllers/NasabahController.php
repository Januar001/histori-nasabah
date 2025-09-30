<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $query = Nasabah::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('namadb', 'like', "%{$search}%")
                  ->orWhere('nocif', 'like', "%{$search}%")
                  ->orWhere('rekening', 'like', "%{$search}%");
        }

        $nasabahs = $query->orderBy('namadb')->paginate(20);

        return view('nasabah.index', compact('nasabahs'));
    }

    public function show($id)
    {
        $nasabah = Nasabah::with(['historyKolektibilitas', 'janjiBayar'])->findOrFail($id);
        return view('nasabah.show', compact('nasabah'));
    }
}
