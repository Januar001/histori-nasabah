<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JanjiBayar extends Model
{
    use HasFactory;
    protected $table = 'janji_bayar';
    protected $fillable = [
        'nasabah_id', 'tanggal_janji', 'nominal_janji', 'status', 'keterangan', 'created_by'
    ];

    // Tambahkan casting untuk tanggal
    protected $casts = [
        'tanggal_janji' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Accessor untuk memastikan selalu return Carbon object
    public function getTanggalJanjiAttribute($value)
    {
        return Carbon::parse($value);
    }

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }
}