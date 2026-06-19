<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kinerja extends Model
{
    use HasFactory;

    protected $table = 'penilaian_kinerja';
    protected $guarded = [];

    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }

    public function kinerjaDetails()
    {
        return $this->hasMany(KinerjaDetail::class, 'kinerja_id');
    }

    public function riwayatJabatan()
    {
        return $this->belongsTo(RiwayatJabatan::class, 'jabatan_id');
    }

    public function riwayatTugas()
    {
        return $this->belongsTo(RiwayatTugas::class, 'tugas_id');
    }
}
