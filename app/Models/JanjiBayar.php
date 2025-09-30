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
        'nasabah_id',
        'tanggal_janji',
        'nominal_janji',
        'status',
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'tanggal_janji' => 'date',
        'nominal_janji' => 'decimal:2'
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }
}