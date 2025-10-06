<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JanjiBayar extends Model
{
    use HasFactory;

    protected $table = 'janji_bayar';

    /**
     * Sesuaikan dengan nama kolom di database Anda.
     */
    protected $fillable = [
        'nasabah_id',
        'tanggal_janji',
        'nominal_janji', // Diubah dari 'nominal'
        'status',
        'keterangan',    // Diubah dari 'catatan'
        'created_by',    // Ditambahkan
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    /**
     * Relasi ke Petugas tidak bisa digunakan karena tidak ada kolom 'petugas_id'.
     * Kita akan memanggil nama petugas melalui kolom 'created_by'.
     * Hapus atau komentari method petugas() jika ada.
     */
    // public function petugas()
    // {
    //     return $this->belongsTo(Petugas::class, 'petugas_id');
    // }
}