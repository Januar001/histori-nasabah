<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\KolektibilitasHistory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();
        Log::info('=== START IMPORT PROCESS for file: ' . $originalFileName . ' ===');

        // 1. Validasi Nama File yang Ketat
        $importDate = null;
        if (preg_match('/(\d{2})_(\d{2})_(\d{4})/', $originalFileName, $matches)) {
            try {
                // $matches[1] -> DD, $matches[2] -> MM, $matches[3] -> YYYY
                $importDate = Carbon::createFromFormat('d_m_Y', $matches[1] . '_' . $matches[2] . '_' . $matches[3])->startOfDay();
            } catch (\Exception $e) {
                return back()->with('error', 'Tanggal pada nama file tidak valid.');
            }
        }

        if (!$importDate) {
            return back()->with('error', 'Proses impor dihentikan. Nama file harus mengandung tanggal dengan format DD_MM_YYYY (contoh: laporan_01_05_2025.xlsx).');
        }

        Log::info('Import date from filename: ' . $importDate->toDateString());

        try {
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $headers = $rows[0] ?? [];
            if (!$this->validateExcelStructure($headers)) {
                return back()->with('error', 'Format header file Excel tidak sesuai. Pastikan kolom nocif, rekening, namadb, dan kualitas ada.');
            }

            array_shift($rows);

            $imported = 0;
            $updated = 0;
            $changesDetected = 0;
            $warnings = [];
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $rowIndex => $row) {
                try {
                    if (empty(array_filter($row))) continue;

                    if (empty($row[3]) || empty($row[4])) {
                        $errors[] = "Baris " . ($rowIndex + 2) . ": Rekening atau Nama nasabah kosong, baris dilewati.";
                        continue;
                    }

                    $rekeningFromFile = $row[3];
                    $namaFromFile = trim($row[4]);
                    $nasabah = Nasabah::where('rekening', $rekeningFromFile)->first();

                    $data = $this->mapRowToData($row);
                    // Gunakan tanggal dari nama file
                    $data['updated_at'] = $importDate;

                    if ($nasabah) {
                        // 2. Proses Pembaruan Data (Update)
                        $namaFromDb = trim($nasabah->namadb);
                        if (strtoupper($namaFromFile) !== strtoupper($namaFromDb)) {
                            $warnings[] = "Baris " . ($rowIndex + 2) . ": Nama nasabah di file ('{$namaFromFile}') tidak cocok dengan database ('{$namaFromDb}') untuk rekening '{$rekeningFromFile}'. Data dilewati.";
                            continue;
                        }

                        $kualitasSebelum = $nasabah->kualitas;
                        $nasabah->update($data);
                        $updated++;

                        // 3. Pencatatan Riwayat (History) untuk Update
                        if ($kualitasSebelum != $data['kualitas']) {
                            KolektibilitasHistory::create([
                                'nasabah_id' => $nasabah->id,
                                'kolektibilitas_sebelum' => $kualitasSebelum,
                                'kolektibilitas_sesudah' => $data['kualitas'],
                                'tanggal_perubahan' => $importDate,
                                'petugas' => 'System Import',
                                'petugas_id' => $nasabah->petugas_id,
                                'keterangan' => 'Auto update dari import Excel tgl ' . $importDate->format('d-m-Y'),
                                'created_at' => $importDate,
                                'updated_at' => $importDate,
                            ]);
                            $changesDetected++;
                        }
                    } else {
                        // 4. Proses Data Baru (Create)
                        $data['created_at'] = $importDate;
                        $newNasabah = Nasabah::create($data);
                        $imported++;

                        // 5. Pencatatan Riwayat (History) untuk Data Baru
                        KolektibilitasHistory::create([
                            'nasabah_id' => $newNasabah->id,
                            'kolektibilitas_sebelum' => '1', // Entri awal dianggap dari '1'
                            'kolektibilitas_sesudah' => $data['kualitas'],
                            'tanggal_perubahan' => $importDate,
                            'petugas' => 'System Import',
                            'keterangan' => 'Data nasabah baru dari import Excel tgl ' . $importDate->format('d-m-Y'),
                            'created_at' => $importDate,
                            'updated_at' => $importDate,
                        ]);
                        // Perubahan kolektibilitas juga dihitung untuk data baru jika kualitasnya bukan 1
                        if ($data['kualitas'] != '1') {
                            $changesDetected++;
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($rowIndex + 2) . ": " . $e->getMessage();
                    continue;
                }
            }

            DB::commit();

            // 6. Umpan Balik Pengguna
            $message = "Import berhasil dari file '{$originalFileName}'. ";
            $summary = [
                'imported' => $imported,
                'updated' => $updated,
                'changesDetected' => $changesDetected,
            ];

            return back()->with('success', $message)
                         ->with('summary', $summary)
                         ->with('warnings', $warnings)
                         ->with('errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Error: ' . $e->getMessage() . ' on file ' . $originalFileName);
            return back()->with('error', 'Terjadi kesalahan fatal saat memproses file: ' . $e->getMessage());
        }
    }
    
    // ... (fungsi lainnya tetap sama) ...
    private function mapRowToData(array $row)
    {
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

        return array_map(function($value) {
            return $value === '' ? null : $value;
        }, $data);
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
            if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                return back()->with('error', 'PhpSpreadsheet library tidak tersedia.');
            }

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

            $colName = function($colIndex) {
                return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            };

            foreach ($headers as $col => $header) {
                $sheet->setCellValue($colName($col) . '1', $header);
            }

            $sampleData = [
                '1', 'KANTOR001', 'CIF001', 'REK001', 'NASABAH CONTOH', '01/01/2024', '01/01/2025',
                '10000000', '10.5', '5000000', '5000000', '0', '500000', '500000', '0',
                '5500000', '1', '5000000', '3000000', '2000000', '500000', '500000', '',
                'Jl. Contoh No. 123', 'Desa Contoh', 'Kecamatan Contoh', 'Kota Contoh',
                'SIFAT01', 'JENIS01', 'KATEGORI01', 'SEKTOR01', 'GUNA01', 'GOL01', 'KREDIT01',
                'PK001', 'Catatan contoh', 'PRODUK01', 'AO01', 'NAMA AO CONTOH', 'BPKB001',
                'SERTIFIKAT001', 'LAINNYA001', 'CIFLAMA001', 'REKLAMA001', 'KONDISI01', '', '5500000'
            ];

            foreach ($sampleData as $col => $value) {
                $sheet->setCellValue($colName($col) . '2', $value);
            }

            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E6E6FA']
                ]
            ];
            $sheet->getStyle('A1:AU1')->applyFromArray($headerStyle);

            foreach (range('A', 'AU') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $templateDir = storage_path('app/templates');
            if (!file_exists($templateDir)) {
                mkdir($templateDir, 0755, true);
            }
            $templatePath = storage_path('app/templates/template_nasabah.xlsx');

            $writer = new Xlsx($spreadsheet);
            $writer->save($templatePath);

            return response()->download($templatePath, 'template_import_nasabah_' . date('Y-m-d') . '.xlsx');

        } catch (\Exception $e) {
            Log::error('Template creation error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat template: ' . $e->getMessage());
        }
    }

    private function parseDate($value)
    {
        if (empty($value) || $value === 'NULL' || $value === 'null' || $value === '-') return null;
        if (is_string($value)) {
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                try { return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d'); } catch (\Exception $e) {}
            }
            if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $value)) {
                try { return Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d'); } catch (\Exception $e) {}
            }
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return $value;
            try { return Carbon::parse($value)->format('Y-m-d'); } catch (\Exception $e) {}
        }
        if (is_numeric($value)) {
            try { return Date::excelToDateTimeObject($value)->format('Y-m-d'); } catch (\Exception $e) { return null; }
        }
        return null;
    }

    private function parseNumeric($value)
    {
        if (empty($value) || $value === 'NULL' || $value === 'null' || $value === '-') return 0;
        if (is_numeric($value)) return (float) $value;
        if (is_string($value)) {
            $cleaned = preg_replace('/[^\d,.-]/', '', $value);
            if (empty($cleaned)) return 0;
            if (strpos($cleaned, ',') !== false && strpos($cleaned, '.') !== false) {
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            } elseif (strpos($cleaned, ',') !== false && strpos($cleaned, '.') === false) {
                if (strpos($cleaned, ',') === strlen($cleaned) - 3) {
                    $cleaned = str_replace(',', '.', $cleaned);
                } else {
                    $cleaned = str_replace(',', '', $cleaned);
                }
            }
            return (float) $cleaned ?: 0;
        }
        return 0;
    }

    public function importStats()
    {
        $thirtyDaysAgo = now()->subDays(30)->startOfDay(); 
        $stats = DB::table('kolektibilitas_history')
            ->where('petugas', 'System Import')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as changes'),
                DB::raw('COUNT(DISTINCT nasabah_id) as unique_nasabahs')
            )
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
            ->where('kh.created_at', '>=', $thirtyDaysAgo) 
            ->groupBy('kh.nasabah_id', 'n.namadb', 'n.nocif', 'n.rekening')
            ->orderBy('change_count', 'desc')
            ->limit(10)
            ->get();
        return view('import.stats', compact('stats', 'topChanges'));
    }

    public function clearHistory(Request $request)
    {
        $request->validate(['confirm' => 'required|boolean']);
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
            if (!in_array(strtolower($col), $headerLower)) return false;
        }
        return true;
    }
}
