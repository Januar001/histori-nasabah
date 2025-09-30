<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KolektibilitasHistory extends Model
{
    use HasFactory;

    protected $table = 'kolektibilitas_history';

    protected $fillable = [
        'nasabah_id',
        'kolektibilitas_sebelum',
        'kolektibilitas_sesudah',
        'tanggal_perubahan',
        'petugas',
        'petugas_id',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_perubahan' => 'date'
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function petugasRelasi()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function getNamaPetugasAttribute()
    {
        return $this->petugasRelasi ? $this->petugasRelasi->nama_petugas : $this->petugas;
    }

    public function getDivisiPetugasAttribute()
    {
        return $this->petugasRelasi ? $this->petugasRelasi->divisi : null;
    }
}