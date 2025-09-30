<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Petugas extends Model
{
    use HasFactory;

    protected $table = 'petugas';

    protected $fillable = [
        'kode_petugas',
        'nama_petugas',
        'divisi',
        'email',
        'telepon',
        'status_aktif'
    ];

    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    public function nasabahs()
    {
        return $this->hasMany(Nasabah::class);
    }

    public function historyKolektibilitas()
    {
        return $this->hasMany(KolektibilitasHistory::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function scopeAo($query)
    {
        return $query->where('divisi', 'AO');
    }

    public function scopeRemedial($query)
    {
        return $query->where('divisi', 'Remedial');
    }

    public function scopeSpecial($query)
    {
        return $query->where('divisi', 'Special');
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }
}
