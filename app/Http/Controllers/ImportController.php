<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\KolektibilitasHistory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet; // Tambahkan import Spreadsheet
use PhpOffice\PhpSpreadsheet\Shared\Date; // Tambahkan import Date
use PhpOffice\PhpSpreadsheet\Style\Fill; // Tambahkan import Fill
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; // Tambahkan import Xlsx

class ImportController extends Controller
{
    public function showImport()
    {
        return view('import.import');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240'
        ]);

        Log::info('=== START IMPORT PROCESS ===');

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $headers = $rows[0] ?? [];
            if (!$this->validateExcelStructure($headers)) {
                return back()->with('error', 'Format file Excel tidak sesuai.');
            }

            array_shift($rows);

            $imported = 0;
            $updated = 0;
            $changesDetected = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $rowIndex => $row) {
                try {
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Periksa kolom 'rekening' (indeks 3) dan 'namadb' (indeks 4)
                    if (empty($row[3]) || empty($row[4])) {
                        $errors[] = "Baris " . ($rowIndex + 2) . ": Rekening atau Nama nasabah kosong";
                        continue;
                    }

                    $nasabah = Nasabah::where('rekening', $row[3])->first();

                    $data = [
                        'no' => $row[0] ?? null,
                        'kantor' => $row[1] ?? null,
                        'nocif' => $row[2] ?? null,
                        'rekening' => $row[3] ?? null,
                        'namadb' => $row[4] ?? null,
                        'tglpinjam' => $this->parseDate($row[5]),
                        'tgltempo' => $this->parseDate($row[6]),
                        'plafon' => $this->parseNumeric($row[7]),
                        'rate' => $this->parseNumeric($row[8]),
                        'nompokok' => $this->parseNumeric($row[9]),
                        'hrpokok' => $this->parseNumeric($row[10]),
                        'xtungpok' => $this->parseNumeric($row[11]),
                        'nombunga' => $this->parseNumeric($row[12]),
                        'hrbunga' => $this->parseNumeric($row[13]),
                        'xtungbu' => $this->parseNumeric($row[14]),
                        'bakidebet' => $this->parseNumeric($row[15]),
                        'kualitas' => $this->parseNumeric($row[16]),
                        'nilckpn' => $this->parseNumeric($row[17]),
                        'nilliquid' => $this->parseNumeric($row[18]),
                        'nilnliquid' => $this->parseNumeric($row[19]),
                        'min_ppap' => $this->parseNumeric($row[20]),
                        'ppapwd' => $this->parseNumeric($row[21]),
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
                        'bakidb' => $this->parseNumeric($row[46]),
                    ];

                    $data = array_map(function($value) {
                        return $value === '' ? null : $value;
                    }, $data);

                    if ($nasabah) {
                        $kualitasChanged = $nasabah->kualitas != $data['kualitas'];
                        $kualitasSebelum = $nasabah->kualitas;

                        $nasabah->update($data);
                        $updated++;

                        if ($kualitasChanged) {
                            KolektibilitasHistory::create([
                                'nasabah_id' => $nasabah->id,
                                'kolektibilitas_sebelum' => $kualitasSebelum,
                                'kolektibilitas_sesudah' => $data['kualitas'],
                                'tanggal_perubahan' => now(),
                                'petugas' => 'System Import',
                                'petugas_id' => $nasabah->petugas_id,
                                'keterangan' => 'Auto update dari import Excel'
                            ]);
                            $changesDetected++;
                            
                            Log::info("HISTORY: {$nasabah->namadb} - {$kualitasSebelum} → {$data['kualitas']}");
                        }

                    } else {
                        Nasabah::create($data);
                        $imported++;
                    }

                } catch (\Exception $e) {
                    // Tangkap error per baris dan lanjutkan
                    $errors[] = "Baris " . ($rowIndex + 2) . ": " . $e->getMessage();
                    continue;
                }
            }

            DB::commit();

            $message = "Import berhasil! ";
            $message .= "Data baru: <strong>{$imported}</strong>, ";
            $message .= "Data update: <strong>{$updated}</strong>";
            
            if ($changesDetected > 0) {
                $message .= ", Perubahan kolektibilitas: <strong>{$changesDetected}</strong>";
            }

            if (!empty($errors)) {
                // Tampilkan hingga 5 error pertama
                $message .= "<br><br><strong>Peringatan (" . count($errors) . " error):</strong><br>" . implode("<br>", array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= "<br>... dan " . (count($errors) - 5) . " error lainnya";
                }
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Import Error: ' . $e->getMessage());

            $errorMessage = 'Error importing file: ' . $e->getMessage();
            
            if (str_contains($e->getMessage(), 'Could not open file')) {
                $errorMessage = 'Tidak dapat membuka file.';
            } elseif (str_contains($e->getMessage(), 'Unable to identify a reader')) {
                $errorMessage = 'Format file tidak didukung.';
            } elseif (str_contains($e->getMessage(), 'Allowed memory size')) {
                $errorMessage = 'File terlalu besar.';
            }

            return back()->with('error', $errorMessage);
        }
    }

    private function parseDate($value)
    {
        if (empty($value) || $value === 'NULL' || $value === 'null' || $value === '-') {
            return null;
        }

        if (is_string($value)) {
            // d/m/Y
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                try {
                    return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
            
            // d-m-Y
            if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $value)) {
                try {
                    return Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
            
            // Y-m-d (sudah format DB)
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }
            
            // Coba parsing universal
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        // Nilai Excel (numeric date)
        if (is_numeric($value)) {
            try {
                // Menggunakan Date::excelToDateTimeObject()
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    private function parseNumeric($value)
    {
        if (empty($value) || $value === 'NULL' || $value === 'null' || $value === '-') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            // Hilangkan semua karakter kecuali angka, koma, dan titik
            $cleaned = preg_replace('/[^\d,.-]/', '', $value);
            
            if (empty($cleaned)) {
                return 0;
            }

            // Kasus 1: Ada koma DAN titik (e.g., 1.000,50 -> harusnya 1000.50)
            if (strpos($cleaned, ',') !== false && strpos($cleaned, '.') !== false) {
                // Asumsi titik sebagai ribuan separator, hapus titik
                $cleaned = str_replace('.', '', $cleaned);
                // Ganti koma dengan titik untuk desimal
                $cleaned = str_replace(',', '.', $cleaned);
            } 
            // Kasus 2: Hanya ada koma (e.g., 1000,50 atau 1,000)
            elseif (strpos($cleaned, ',') !== false && strpos($cleaned, '.') === false) {
                // Jika koma berada 3 karakter dari belakang (misalnya 1000,50), asumsikan desimal
                if (strpos($cleaned, ',') === strlen($cleaned) - 3) {
                    $cleaned = str_replace(',', '.', $cleaned);
                } else {
                    // Jika tidak (misalnya 1,000), asumsikan ribuan separator, hapus koma
                    $cleaned = str_replace(',', '', $cleaned);
                }
            }
            // Kasus 3: Hanya ada titik (e.g., 1000.50 atau 1.000) - Biarkan, PHP akan tangani

            $numeric = (float) $cleaned;
            return $numeric ?: 0;
        }

        return 0;
    }

    public function importHistory()
    {
        $imports = KolektibilitasHistory::where('petugas', 'System Import')
            ->with(['nasabah', 'petugasRelasi'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $summary = DB::table('kolektibilitas_history')
            ->where('petugas', 'System Import')
            ->select(
                DB::raw('COUNT(*) as total_imports'),
                DB::raw('COUNT(DISTINCT DATE(created_at)) as total_days'),
                DB::raw('MAX(created_at) as last_import')
            )
            ->first();

        return view('import.history', compact('imports', 'summary'));
    }

    public function downloadTemplate()
    {
        $templatePath = storage_path('app/templates/template_nasabah.xlsx');
        
        if (!file_exists($templatePath)) {
            return $this->createBasicTemplate();
        }

        return response()->download($templatePath, 'template_import_nasabah_' . date('Y-m-d') . '.xlsx');
    }

    private function createBasicTemplate()
    {
        try {
            // Gunakan class yang sudah di-import di atas
            if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                return back()->with('error', 'PhpSpreadsheet library tidak tersedia.');
            }

            // Inisiasi Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = [
                'no', 'kantor', 'nocif', 'rekening', 'namadb', 'tglpinjam', 'tgltempo',
                'plafon', 'rate', 'nompokok', 'hrpokok', 'xtungpok', 'nombunga', 'hrbunga',
                'xtungbu', 'bakidebet', 'kualitas', 'nilckpn', 'nilliquid', 'nilnliquid',
                'min_ppap', 'ppapwd', 'tgl_macet', 'alamat', 'desa', 'kecamatan', 'dati2',
                'sifat', 'jenis', 'kategori_deb', 'sektor', 'jnsguna', 'goldeb', 'jnskre',
                'nopk', 'catatan', 'ketproduk', 'kdao', 'namaao', 'jbpkb', 'jsertifikat',
                'jlain2', 'ciflama', 'rekeninglama', 'kdkondisi', 'tglunas', 'bakidb'
            ];

            // Mapping dari indeks kolom ke huruf kolom Excel (A, B, C, ...)
            $colName = function($colIndex) {
                return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            };

            // PERBAIKAN: Gunakan setCellValue untuk Header
            foreach ($headers as $col => $header) {
                // Metode setCellValueByColumnAndRow seharusnya bekerja, tapi kita pastikan.
                // Jika error masih terjadi, coba ganti baris di bawah dengan yang di komentar
                // $sheet->setCellValueByColumnAndRow($col + 1, 1, $header); 
                $sheet->setCellValue($colName($col) . '1', $header); // Alternatif jika error
            }

            $sampleData = [
                '1', 'KANTOR001', 'CIF001', 'REK001', 'NASABAH CONTOH', '01/01/2024', '01/01/2025',
                '10000000', '10.5', '5000000', '5000000', '0', '500000', '500000', '0',
                '5500000', '1', '5000000', '3000000', '2000000', '500000', '500000', '',
                'Jl. Contoh No. 123', 'Desa Contoh', 'Kecamatan Contoh', 'Kota Contoh',
                'SIFAT01', 'JENIS01', 'KATEGORI01', 'SEKTOR01', 'GUNA01', 'GOL01', 'KREDIT01',
                'PK001', 'Catatan contoh', 'PRODUK01', 'AO01', 'NAMA AO CONTOH', 'BPKB001',
                'SERTIFIKAT001', 'LAINNYA001', 'CIFLAMA001', 'REKLAMA001', 'KONDISI01', '', '5500000' // Tambahkan bakidb
            ];

            // PERBAIKAN: Gunakan setCellValue untuk Sample Data
            foreach ($sampleData as $col => $value) {
                // Metode setCellValueByColumnAndRow seharusnya bekerja, tapi kita pastikan.
                // $sheet->setCellValueByColumnAndRow($col + 1, 2, $value);
                $sheet->setCellValue($colName($col) . '2', $value); // Alternatif jika error
            }

            // Styling Header
            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID, // Gunakan Fill yang sudah di-import
                    'startColor' => ['rgb' => 'E6E6FA']
                ]
            ];
            // Koordinat akhir kolom sudah benar (AU)
            $sheet->getStyle('A1:AU1')->applyFromArray($headerStyle);

            // Auto-size kolom
            foreach (range('A', 'AU') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Simpan file
            $templateDir = storage_path('app/templates');
            if (!file_exists($templateDir)) {
                mkdir($templateDir, 0755, true);
            }

            $templatePath = storage_path('app/templates/template_nasabah.xlsx');

            // Gunakan Xlsx yang sudah di-import
            $writer = new Xlsx($spreadsheet);
            $writer->save($templatePath);

            return response()->download($templatePath, 'template_import_nasabah_' . date('Y-m-d') . '.xlsx');

        } catch (\Exception $e) {
            Log::error('Template creation error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat template: ' . $e->getMessage());
        }
    }

    public function importStats()
    {
        // Variabel ini akan mencakup seluruh hari (hingga 30 hari ke belakang)
        $thirtyDaysAgo = now()->subDays(30)->startOfDay(); 

        $stats = DB::table('kolektibilitas_history')
            ->where('petugas', 'System Import')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as changes'),
                DB::raw('COUNT(DISTINCT nasabah_id) as unique_nasabahs')
            )
            // Menggunakan $thirtyDaysAgo untuk memastikan mencakup waktu awal hari
            ->where('created_at', '>=', $thirtyDaysAgo) 
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $topChanges = DB::table('kolektibilitas_history as kh')
            ->join('nasabahs as n', 'kh.nasabah_id', '=', 'n.id')
            ->where('kh.petugas', 'System Import')
            ->select(
                'n.namadb',
                'n.nocif',
                'n.rekening',
                DB::raw('COUNT(kh.id) as change_count')
            )
            // Menggunakan $thirtyDaysAgo untuk konsistensi filter waktu
            ->where('kh.created_at', '>=', $thirtyDaysAgo) 
            ->groupBy('kh.nasabah_id', 'n.namadb', 'n.nocif', 'n.rekening')
            ->orderBy('change_count', 'desc')
            ->limit(10)
            ->get();

        return view('import.stats', compact('stats', 'topChanges'));
    }


    public function clearHistory(Request $request)
    {
        $request->validate([
            'confirm' => 'required|boolean'
        ]);

        if (!$request->confirm) {
            return back()->with('error', 'Konfirmasi diperlukan untuk menghapus history.');
        }

        try {
            $deleted = KolektibilitasHistory::where('petugas', 'System Import')
                ->where('created_at', '<', now()->subMonths(3))
                ->delete();

            return back()->with('success', "Berhasil menghapus {$deleted} records history import yang lama.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus history: ' . $e->getMessage());
        }
    }

    private function validateExcelStructure($headers)
    {
        $requiredColumns = ['nocif', 'rekening', 'namadb', 'kualitas'];
        
        $headerLower = array_map('strtolower', $headers);
        
        foreach ($requiredColumns as $col) {
            if (!in_array(strtolower($col), $headerLower)) {
                return false;
            }
        }

        return true;
    }
}