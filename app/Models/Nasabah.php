<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabahs';

    protected $fillable = [
        'no',
        'kantor',
        'nocif',
        'rekening',
        'namadb',
        'tglpinjam',
        'tgltempo',
        'plafon',
        'rate',
        'nompokok',
        'hrpokok',
        'xtungpok',
        'nombunga',
        'hrbunga',
        'xtungbu',
        'bakidebet',
        'kualitas',
        'nilckpn',
        'nilliquid',
        'nilnliquid',
        'min_ppap',
        'ppapwd',
        'tgl_macet',
        'alamat',
        'desa',
        'kecamatan',
        'dati2',
        'sifat',
        'jenis',
        'kategori_deb',
        'sektor',
        'jnsguna',
        'goldeb',
        'jnskre',
        'nopk',
        'catatan',
        'ketproduk',
        'kdao',
        'namaao',
        'jbpkb',
        'jsertifikat',
        'jlain2',
        'ciflama',
        'rekeninglama',
        'kdkondisi',
        'tglunas',
        'bakidb',
        'petugas_id',
        'tanggal_ditangani',
        'catatan_penanganan'
    ];

    protected $casts = [
        'tglpinjam' => 'date',
        'tgltempo' => 'date',
        'tgl_macet' => 'date',
        'tglunas' => 'date',
        'tanggal_ditangani' => 'date',
        'plafon' => 'decimal:2',
        'rate' => 'decimal:2',
        'nompokok' => 'decimal:2',
        'hrpokok' => 'decimal:2',
        'xtungpok' => 'decimal:2',
        'nombunga' => 'decimal:2',
        'hrbunga' => 'decimal:2',
        'xtungbu' => 'decimal:2',
        'bakidebet' => 'decimal:2',
        'nilckpn' => 'decimal:2',
        'nilliquid' => 'decimal:2',
        'nilnliquid' => 'decimal:2',
        'min_ppap' => 'decimal:2',
        'ppapwd' => 'decimal:2',
        'bakidb' => 'decimal:2'
    ];

    public function petugas()
    {
        return $this->belongsTo(Petugas::class);
    }

    public function historyKolektibilitas()
    {
        return $this->hasMany(KolektibilitasHistory::class);
    }

    public function janjiBayar()
    {
        return $this->hasMany(JanjiBayar::class);
    }

    public function getDivisiPenangananAttribute()
    {
        return $this->petugas ? $this->petugas->divisi : null;
    }
}