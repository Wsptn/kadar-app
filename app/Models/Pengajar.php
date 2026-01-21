<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajar extends Model
{
    // Pastikan nama tabel di database benar 'pengajar' (bukan 'pengajars')
    protected $table = 'pengajar';

    // WAJIB: Izinkan kolom ini diisi massal oleh Controller
    protected $fillable = [
        'pengurus_id',
        'status',
        'nama',
        'niup',
    ];

    /**
     * Relasi ke Model Pengurus
     * Untuk mengambil data nama, niup, foto, dll.
     */
    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }
}
