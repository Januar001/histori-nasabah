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
    private function getColumnFriendlyNames(): array
    {
        return [
            'no' => 'No',
            'kantor' => 'Kantor',
            'nocif' => 'No. CIF',
            'rekening' => 'No. Rekening',
            'namadb' => 'Nama Nasabah',
            'tglpinjam' => 'Tanggal Pinjam',
            'tgltempo' => 'Tanggal Tempo',
            'plafon' => 'Plafon',
            'rate' => 'Suku Bunga',
            'nompokok' => 'Nominal Pokok',
            'hrpokok' => 'Hari Pokok',
            'xtungpok' => 'Tunggakan Pokok (x)',
            'nombunga' => 'Nominal Bunga',
            'hrbunga' => 'Hari Bunga',
            'xtungbu' => 'Tunggakan Bunga (x)',
            'bakidebet' => 'Baki Debet',
            'kualitas' => 'Kualitas',
            'nilckpn' => 'Nilai CKPN',
            'nilliquid' => 'Nilai Likuid',
            'nilnliquid' => 'Nilai Non-Likuid',
            'min_ppap' => 'Min PPAP',
            'ppapwd' => 'PPAP Wajib Dibentuk',
            'tgl_macet' => 'Tanggal Macet',
            'alamat' => 'Alamat',
            'desa' => 'Desa',
            'kecamatan' => 'Kecamatan',
            'dati2' => 'Dati 2',
            'sifat' => 'Sifat',
            'jenis' => 'Jenis',
            'kategori_deb' => 'Kategori Debitur',
            'sektor' => 'Sektor',
            'jnsguna' => 'Jenis Penggunaan',
            'goldeb' => 'Golongan Debitur',
            'jnskre' => 'Jenis Kredit',
            'nopk' => 'No. PK',
            'catatan' => 'Catatan',
            'ketproduk' => 'Keterangan Produk',
            'kdao' => 'Kode AO',
            'namaao' => 'Nama AO',
            'jbpkb' => 'Jaminan BPKB',
            'jsertifikat' => 'Jaminan Sertifikat',
            'jlain2' => 'Jaminan Lainnya',
            'ciflama' => 'CIF Lama',
            'rekeninglama' => 'Rekening Lama',
            'kdkondisi' => 'Kode Kondisi',
            'tglunas' => 'Tanggal Lunas',
            'bakidb' => 'Baki DB',
            'petugas_id' => 'ID Petugas',
            'tanggal_ditangani' => 'Tanggal Ditangani',
            'catatan_penanganan' => 'Catatan Penanganan',
        ];
    }
    
    public function showImport()
    {
        return view('import.import');
    }

    public function importExcel(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls|max:10240']);
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
                    if (empty(array_filter($row))) continue;
                    if (empty($row[3]) || empty($row[4])) {
                        $errors[] = "Baris " . ($rowIndex + 2) . ": Rekening atau Nama nasabah kosong";
                        continue;
                    }
                    $data = [
                        'no' => $row[0] ?? null, 'kantor' => $row[1] ?? null, 'nocif' => $row[2] ?? null, 'rekening' => $row[3] ?? null, 'namadb' => $row[4] ?? null, 'tglpinjam' => $this->parseDate($row[5]), 'tgltempo' => $this->parseDate($row[6]), 'plafon' => $this->parseNumeric($row[7]), 'rate' => $this->parseNumeric($row[8]), 'nompokok' => $this->parseNumeric($row[9]), 'hrpokok' => $this->parseNumeric($row[10]), 'xtungpok' => $this->parseNumeric($row[11]), 'nombunga' => $this->parseNumeric($row[12]), 'hrbunga' => $this->parseNumeric($row[13]), 'xtungbu' => $this->parseNumeric($row[14]), 'bakidebet' => $this->parseNumeric($row[15]), 'kualitas' => $this->parseNumeric($row[16]), 'nilckpn' => $this->parseNumeric($row[17]), 'nilliquid' => $this->parseNumeric($row[18]), 'nilnliquid' => $this->parseNumeric($row[19]), 'min_ppap' => $this->parseNumeric($row[20]), 'ppapwd' => $this->parseNumeric($row[21]), 'tgl_macet' => $this->parseDate($row[22]), 'alamat' => $row[23] ?? null, 'desa' => $row[24] ?? null, 'kecamatan' => $row[25] ?? null, 'dati2' => $row[26] ?? null, 'sifat' => $row[27] ?? null, 'jenis' => $row[28] ?? null, 'kategori_deb' => $row[29] ?? null, 'sektor' => $row[30] ?? null, 'jnsguna' => $row[31] ?? null, 'goldeb' => $row[32] ?? null, 'jnskre' => $row[33] ?? null, 'nopk' => $row[34] ?? null, 'catatan' => $row[35] ?? null, 'ketproduk' => $row[36] ?? null, 'kdao' => $row[37] ?? null, 'namaao' => $row[38] ?? null, 'jbpkb' => $row[39] ?? null, 'jsertifikat' => $row[40] ?? null, 'jlain2' => $row[41] ?? null, 'ciflama' => $row[42] ?? null, 'rekeninglama' => $row[43] ?? null, 'kdkondisi' => $row[44] ?? null, 'tglunas' => $this->parseDate($row[45]), 'bakidb' => $this->parseNumeric($row[46]),
                    ];
                    $data = array_map(fn($v) => $v === '' ? null : $v, $data);
                    $nasabah = Nasabah::where('rekening', $row[3])->first();

                    if ($nasabah) {
                        $nasabah->fill($data);
                        if ($nasabah->isDirty()) {
                            $changedAttributes = $nasabah->getDirty();

                            if (array_key_exists('no', $changedAttributes)) {
                                unset($changedAttributes['no']);
                            }

                            if (!empty($changedAttributes)) {
                                $originalKualitas = $nasabah->getOriginal('kualitas');
                                $friendlyNames = $this->getColumnFriendlyNames();
                                $changeDetails = [];
                                
                                $currencyFields = ['plafon', 'nompokok', 'nombunga', 'bakidebet', 'nilckpn', 'bakidb', 'nilliquid', 'nilnliquid', 'min_ppap', 'ppapwd'];
                                $integerFields = ['hrpokok', 'xtungpok', 'hrbunga', 'xtungbu'];

                                foreach ($changedAttributes as $field => $newValue) {
                                    $friendlyField = $friendlyNames[$field] ?? $field;
                                    $oldValue = $nasabah->getOriginal($field);
                                    $detailString = "• <b>{$friendlyField}:</b> ";

                                    if (is_numeric($oldValue) || is_numeric($newValue)) {
                                        $oldNum = (float) $oldValue;
                                        $newNum = (float) $newValue;
                                        $diff = $newNum - $oldNum;

                                        if (in_array($field, $currencyFields)) {
                                            $oldFormatted = 'Rp ' . number_format($oldNum, 0, ',', '.');
                                            $newFormatted = 'Rp ' . number_format($newNum, 0, ',', '.');
                                            $diffFormatted = 'Rp ' . number_format(abs($diff), 0, ',', '.');
                                        } else if (in_array($field, $integerFields)) {
                                            $oldFormatted = number_format($oldNum, 0, ',', '.');
                                            $newFormatted = number_format($newNum, 0, ',', '.');
                                            $diffFormatted = number_format(abs($diff), 0, ',', '.');
                                        } else {
                                            $oldFormatted = (string) $oldNum;
                                            $newFormatted = (string) $newNum;
                                            $diffFormatted = (string) abs($diff);
                                        }
                                        
                                        $detailString .= "{$oldFormatted} → {$newFormatted} ";

                                        if ($diff < 0) {
                                            $detailString .= "<span class=\"badge bg-success\">-{$diffFormatted}</span>";
                                        } else if ($diff > 0) {
                                            $detailString .= "<span class=\"badge bg-danger\">+{$diffFormatted}</span>";
                                        }
                                    } else {
                                        $oldFormatted = $oldValue ?? 'Kosong';
                                        $newFormatted = $newValue ?? 'Kosong';
                                        $detailString .= "{$oldFormatted} → {$newFormatted}";
                                    }
                                    $changeDetails[] = $detailString;
                                }
                                $keterangan = "Perubahan data:<br>" . implode("<br>", $changeDetails);

                                KolektibilitasHistory::create([
                                    'nasabah_id' => $nasabah->id, 'kolektibilitas_sebelum' => $originalKualitas, 'kolektibilitas_sesudah' => $nasabah->kualitas, 'tanggal_perubahan' => now(), 'petugas' => 'System Import', 'petugas_id' => $nasabah->petugas_id, 'keterangan' => $keterangan
                                ]);
                                $changesDetected++;
                            }
                            
                            $nasabah->save();
                            $updated++;
                        }
                    } else {
                        $newNasabah = Nasabah::create($data);
                        $imported++;
                        $changesDetected++;
                        KolektibilitasHistory::create([
                            'nasabah_id' => $newNasabah->id, 'kolektibilitas_sebelum' => $data['kualitas'], 'kolektibilitas_sesudah' => $data['kualitas'], 'tanggal_perubahan' => now(), 'petugas' => 'System Import', 'petugas_id' => null, 'keterangan' => 'Nasabah baru ditambahkan'
                        ]);
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($rowIndex + 2) . ": " . $e->getMessage();
                    continue;
                }
            }

            DB::commit();
            $message = "Import berhasil! Data baru: <strong>{$imported}</strong>, Data diupdate: <strong>{$updated}</strong>. Total data tercatat di history: <strong>{$changesDetected}</strong>.";
            if (!empty($errors)) {
                $message .= "<br><br><strong>Peringatan (" . count($errors) . " error):</strong><br>" . implode("<br>", array_slice($errors, 0, 5));
                if (count($errors) > 5) $message .= "<br>... dan " . (count($errors) - 5) . " error lainnya";
            }
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal melakukan import: ' . $e->getMessage());
        }
    }

    private function parseDate($value)
    {
        if (empty($value) || $value === 'NULL' || $value === 'null' || $value === '-') {
            return null;
        }
        if (is_string($value)) {
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                try { return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d'); } catch (\Exception $e) {}
            }
            if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $value)) {
                try { return Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d'); } catch (\Exception $e) {}
            }
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) { return $value; }
            try { return Carbon::parse($value)->format('Y-m-d'); } catch (\Exception $e) {}
        }
        if (is_numeric($value)) {
            try { return Date::excelToDateTimeObject($value)->format('Y-m-d'); } catch (\Exception $e) { return null; }
        }
        return null;
    }

    private function parseNumeric($value)
    {
        if (empty($value) || $value === 'NULL' || $value === 'null' || $value === '-') { return 0; }
        if (is_numeric($value)) { return (float) $value; }
        if (is_string($value)) {
            $cleaned = preg_replace('/[^\d,.-]/', '', $value);
            if (empty($cleaned)) { return 0; }
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
                'no', 'kantor', 'nocif', 'rekening', 'namadb', 'tglpinjam', 'tgltempo', 'plafon', 'rate', 'nompokok', 'hrpokok', 'xtungpok', 'nombunga', 'hrbunga', 'xtungbu', 'bakidebet', 'kualitas', 'nilckpn', 'nilliquid', 'nilnliquid', 'min_ppap', 'ppapwd', 'tgl_macet', 'alamat', 'desa', 'kecamatan', 'dati2', 'sifat', 'jenis', 'kategori_deb', 'sektor', 'jnsguna', 'goldeb', 'jnskre', 'nopk', 'catatan', 'ketproduk', 'kdao', 'namaao', 'jbpkb', 'jsertifikat', 'jlain2', 'ciflama', 'rekeninglama', 'kdkondisi', 'tglunas', 'bakidb'
            ];
            $colName = fn($colIndex) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($colName($col) . '1', $header);
            }
            $headerStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6E6FA']]];
            $sheet->getStyle('A1:AU1')->applyFromArray($headerStyle);
            foreach (range('A', 'AU') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            $templateDir = storage_path('app/templates');
            if (!file_exists($templateDir)) { mkdir($templateDir, 0755, true); }
            $templatePath = storage_path('app/templates/template_nasabah.xlsx');
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
        $thirtyDaysAgo = now()->subDays(30)->startOfDay();
        $stats = DB::table('kolektibilitas_history')
            ->where('petugas', 'System Import')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as changes'), DB::raw('COUNT(DISTINCT nasabah_id) as unique_nasabahs'))
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')->orderBy('date', 'desc')->get();
        $topChanges = DB::table('kolektibilitas_history as kh')
            ->join('nasabahs as n', 'kh.nasabah_id', '=', 'n.id')
            ->where('kh.petugas', 'System Import')
            ->select('n.namadb', 'n.nocif', 'n.rekening', DB::raw('COUNT(kh.id) as change_count'))
            ->where('kh.created_at', '>=', $thirtyDaysAgo)
            ->groupBy('kh.nasabah_id', 'n.namadb', 'n.nocif', 'n.rekening')
            ->orderBy('change_count', 'desc')->limit(10)->get();
        return view('import.stats', compact('stats', 'topChanges'));
    }

    public function clearHistory(Request $request)
    {
        $request->validate(['confirm' => 'required|boolean']);
        if (!$request->confirm) {
            return back()->with('error', 'Konfirmasi diperlukan untuk menghapus history.');
        }
        try {
            $deleted = KolektibilitasHistory::where('petugas', 'System Import')->where('created_at', '<', now()->subMonths(3))->delete();
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
            if (!in_array(strtolower($col), $headerLower)) { return false; }
        }
        return true;
    }
}