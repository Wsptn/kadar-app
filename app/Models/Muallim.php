<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Muallim extends Model
{
    // Pastikan nama tabel sesuai dengan yang ada di database
    protected $table = 'muallim';

    // WAJIB: Daftarkan kolom ini agar bisa diisi otomatis oleh Controller
    protected $fillable = [
        'pengurus_id',
        'status',
        'nama',
        'niup',
    ];

    /**
     * Relasi ke Model Pengurus
     * Agar kita bisa memanggil data nama, niup, foto dari tabel pengurus
     */
    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }

    /**
     * Relasi ke Fungsional Tugas (Opsional, lewat pengurus)
     * Karena muallim terhubung ke pengurus, dan pengurus punya banyak tugas.
     */
    public function fungsionalTugas()
    {
        // Ini trick untuk mengambil data fungsional tugas lewat relasi pengurus
        return $this->hasManyDeep(
            MasterFungsionalTugas::class,
            [Pengurus::class, 'pengurus_fungsional_tugas'],
            ['id', 'pengurus_id', 'id_tugas'],
            ['pengurus_id', 'id', 'master_fungsional_tugas_id']
        );
    }
}
