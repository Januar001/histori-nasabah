<?php

namespace App\Imports;

use App\Models\Nasabah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;


class NasabahImport implements ToModel, WithStartRow, WithBatchInserts, WithChunkReading
{
    private $tanggalLaporan;

    public function __construct(string $tanggalLaporan)
    {
        $this->tanggalLaporan = $tanggalLaporan;
    }

    public function model(array $row)
    {
        try {
            return new Nasabah([
                'tanggal_laporan' => $this->tanggalLaporan,
                'kantor' => $row[0] ?? null,
                'nocif' => $row[1] ?? null,
                'rekening' => $row[2] ?? null,
                'namadb' => $row[3] ?? null,
                'tglpinjam' => isset($row[4]) ? Date::excelToDateTimeObject($row[4])->format('Y-m-d') : null,
                'tgltempo' => isset($row[5]) ? Date::excelToDateTimeObject($row[5])->format('Y-m-d') : null,
                'plafon' => $this->parseNumeric($row[6]),
                'bakidebet' => $this->parseNumeric($row[7]),
                'kualitas' => $this->parseNumeric($row[8]),
                'hrpokok' => $this->parseNumeric($row[9]),
                'xtungpok' => $this->parseNumeric($row[10]),
                'hrbunga' => $this->parseNumeric($row[11]),
                'xtungbu' => $this->parseNumeric($row[12]),
                'denda' => $this->parseNumeric($row[13]),
                'total_tunggakan' => $this->parseNumeric($row[14]),
                'alamat' => $row[15] ?? null,
                'telepon' => $row[16] ?? null,
                'pekerjaan' => $row[17] ?? null,
                'nama_ao' => $row[18] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error parsing row: ' . json_encode($row) . ' | Error: ' . $e->getMessage());
            return null;
        }
    }
    
    private function parseNumeric($value)
    {
        return is_numeric($value) ? $value : 0;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}