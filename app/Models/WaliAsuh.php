<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaliAsuh extends Model
{
    // Pastikan nama tabel sesuai database Anda (biasanya 'wali_asuh' atau 'wali_asuhs')
    protected $table = 'wali_asuh';

    // WAJIB: Agar bisa diisi otomatis lewat Controller (Mass Assignment)
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
