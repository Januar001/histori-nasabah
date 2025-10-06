<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NasabahImport;
use Carbon\Carbon;

class ImportController extends Controller
{
    public function showForm()
    {
        return view('import.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        preg_match('/(\d{4}-\d{2}-\d{2})/', $fileName, $matches);

        if (!isset($matches[1])) {
            return back()->with('error', 'Format nama file salah. Pastikan mengandung tanggal (YYYY-MM-DD). Contoh: data_2025-10-06.xlsx');
        }

        try {
            $reportDate = Carbon::createFromFormat('Y-m-d', $matches[1])->format('Y-m-d');
        } catch (\Exception $e) {
            return back()->with('error', 'Tanggal dalam nama file tidak valid.');
        }

        try {
            Excel::import(new NasabahImport($reportDate), $file);
            return back()->with('success', 'Data untuk tanggal ' . $reportDate . ' berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat impor: ' . $e->getMessage());
        }
    }
}