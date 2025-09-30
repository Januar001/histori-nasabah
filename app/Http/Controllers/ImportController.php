<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\KolektibilitasHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function showImport()
    {
        return view('import.import');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            $imported = 0;
            $updated = 0;

            foreach ($rows as $row) {
                if (empty($row[3])) continue; // Skip if no rekening

                $nasabah = Nasabah::where('rekening', $row[3])->first();

                $data = [
                    'no' => $row[0] ?? null,
                    'kantor' => $row[1] ?? null,
                    'nocif' => $row[2] ?? null,
                    'rekening' => $row[3] ?? null,
                    'namadb' => $row[4] ?? null,
                    'tglpinjam' => $this->parseDate($row[5]),
                    'tgltempo' => $this->parseDate($row[6]),
                    'plafon' => $row[7] ?? 0,
                    'rate' => $row[8] ?? 0,
                    'nompokok' => $row[9] ?? 0,
                    'hrpokok' => $row[10] ?? 0,
                    'xtungpok' => $row[11] ?? 0,
                    'nombunga' => $row[12] ?? 0,
                    'hrbunga' => $row[13] ?? 0,
                    'xtungbu' => $row[14] ?? 0,
                    'bakidebet' => $row[15] ?? 0,
                    'kualitas' => $row[16] ?? null,
                    'nilckpn' => $row[17] ?? 0,
                    'nilliquid' => $row[18] ?? 0,
                    'nilnliquid' => $row[19] ?? 0,
                    'min_ppap' => $row[20] ?? 0,
                    'ppapwd' => $row[21] ?? 0,
                    'tgl_macet' => $this->parseDate($row[22]),
                    'alamat' => $row[23] ?? null,
                    'desa' => $row[24] ?? null,
                    'kecamatan' => $row[25] ?? null,
                    'dati2' => $row[26] ?? null,
                    'sifat' => $row[27] ?? null,
                    'jenis' => $row[28] ?? null,
                    'kategori_deb' => $row[29] ?? null,
                    'sektor' => $row[30] ?? null,
                    'jnsguna' => $row[31] ?? null,
                    'goldeb' => $row[32] ?? null,
                    'jnskre' => $row[33] ?? null,
                    'nopk' => $row[34] ?? null,
                    'catatan' => $row[35] ?? null,
                    'ketproduk' => $row[36] ?? null,
                    'kdao' => $row[37] ?? null,
                    'namaao' => $row[38] ?? null,
                    'jbpkb' => $row[39] ?? null,
                    'jsertifikat' => $row[40] ?? null,
                    'jlain2' => $row[41] ?? null,
                    'ciflama' => $row[42] ?? null,
                    'rekeninglama' => $row[43] ?? null,
                    'kdkondisi' => $row[44] ?? null,
                    'tglunas' => $this->parseDate($row[45]),
                    'bakidb' => $row[46] ?? 0,
                ];

                if ($nasabah) {
                    // Check if kualitas changed
                    if ($nasabah->kualitas != $data['kualitas']) {
                        KolektibilitasHistory::create([
                            'nasabah_id' => $nasabah->id,
                            'kolektibilitas_sebelum' => $nasabah->kualitas,
                            'kolektibilitas_sesudah' => $data['kualitas'],
                            'tanggal_perubahan' => now(),
                            'petugas' => 'System Import',
                            'keterangan' => 'Auto update dari import Excel'
                        ]);
                    }

                    $nasabah->update($data);
                    $updated++;
                } else {
                    Nasabah::create($data);
                    $imported++;
                }
            }

            return back()->with('success', "Import berhasil! Data baru: $imported, Data update: $updated");

        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;
        
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        
        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}