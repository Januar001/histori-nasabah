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
        'nasabah_id', 'kolektibilitas_sebelum', 'kolektibilitas_sesudah', 
        'tanggal_perubahan', 'petugas', 'keterangan'
    ];

    // Tambahkan casting untuk tanggal
    protected $casts = [
        'tanggal_perubahan' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    // Accessor untuk memastikan selalu return Carbon object
    public function getTanggalPerubahanAttribute($value)
    {
        return Carbon::parse($value);
    }
}