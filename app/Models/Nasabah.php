<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nasabah extends Model
{
    use HasFactory;

    protected $fillable = [
        'no', 'kantor', 'nocif', 'rekening', 'namadb', 'tglpinjam', 'tgltempo', 
        'plafon', 'rate', 'nompokok', 'hrpokok', 'xtungpok', 'nombunga', 'hrbunga', 
        'xtungbu', 'bakidebet', 'kualitas', 'nilckpn', 'nilliquid', 'nilnliquid', 
        'min_ppap', 'ppapwd', 'tgl_macet', 'alamat', 'desa', 'kecamatan', 'dati2', 
        'sifat', 'jenis', 'kategori_deb', 'sektor', 'jnsguna', 'goldeb', 'jnskre', 
        'nopk', 'catatan', 'ketproduk', 'kdao', 'namaao', 'jbpkb', 'jsertifikat', 
        'jlain2', 'ciflama', 'rekeninglama', 'kdkondisi', 'tglunas', 'bakidb'
    ];

    public function historyKolektibilitas()
    {
        return $this->hasMany(KolektibilitasHistory::class);
    }

    public function janjiBayar()
    {
        return $this->hasMany(JanjiBayar::class);
    }
}
