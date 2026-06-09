<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengurus extends Model
{
    protected $table = 'pengurus';

    protected $fillable = [
        'niup',
        'nama',
        'kamar_id',
        'entitas_daerah',
        'jabatan_id',
        'sk_kepengurusan',
        'status',
        'pendidikan_id',
        'tgl_mulai_tugas',

        // berkas / file
        'berkas_sk_pengurus',
        'berkas_surat_tugas',
        'berkas_plt',
        'berkas_lain',
        'foto',
    ];

    // === RELATIONS ===

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function strukturJabatan()
    {
        return $this->belongsTo(MasterStrukturJabatan::class, 'jabatan_id');
    }

    /**
     * Relasi ke semua Master Tugas (Fungsional, Internal, Eksternal)
     */
    public function tugas()
    {
        return $this->belongsToMany(
            MasterTugas::class,
            'tugas_detail',
            'pengurus_id',
            'tugas_id',
            'id',
            'id'
        )
            ->withPivot('status', 'tgl_mulai', 'tgl_selesai')
            ->wherePivot('status', 'aktif')
            ->withTimestamps();
    }

    public function fungsionalTugas()
    {
        return $this->tugas()->where('jenis_tugas', 'fungsional');
    }

    public function internalTugas()
    {
        return $this->tugas()->where('jenis_tugas', 'internal');
    }

    public function eksternalTugas()
    {
        return $this->tugas()->where('jenis_tugas', 'eksternal');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id');
    }


    public function kinerja()
    {
        return $this->hasMany(Kinerja::class, 'pengurus_id');
    }

    public function riwayatJabatans()
    {
        return $this->hasMany(RiwayatJabatan::class, 'pengurus_id')->orderBy('tgl_mulai', 'desc');
    }

    public function riwayatTugas()
    {
        return $this->hasMany(RiwayatTugas::class, 'pengurus_id')->orderBy('tgl_mulai', 'desc');
    }

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'pengurus_id')->orderBy('tanggal_mulai', 'desc');
    }
}
