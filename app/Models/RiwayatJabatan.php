<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatJabatan extends Model
{
    protected $table = 'riwayat_jabatans';

    protected $fillable = [
        'pengurus_id',
        'struktur_jabatan_id',
        'tgl_mulai',
        'tgl_selesai',
        'status',
    ];

    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }

    public function strukturJabatan()
    {
        return $this->belongsTo(MasterStrukturJabatan::class, 'struktur_jabatan_id');
    }
}
