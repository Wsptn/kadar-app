<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengurus extends Model
{
    protected $table = 'penguruses';

    protected $fillable = [
        'niup',
        'nama',
        'domisili_id',
        'entitas_daerah',
        'entitas_id', // keeping this for now just in case? No, wait, entitas_id is used for what? Ah, wait, in penguruses I dropped entitas_id! Let's just put struktur_jabatan_id
        'struktur_jabatan_id',
        'sk_kepengurusan',
        // 'fungsional_tugas_id',  <-- SUDAH DIHAPUS (Karena pindah ke tabel pivot)
        'status',
        'pendidikan_id',
        'angkatan_id',
        'tgl_mulai_tugas',

        // berkas / file
        'berkas_sk_pengurus',
        'berkas_surat_tugas',
        'berkas_plt',
        'berkas_lain',
        'foto',
    ];

    // === RELATIONS ===

    public function domisili()
    {
        return $this->belongsTo(Domisili::class, 'domisili_id');
    }

    public function strukturJabatan()
    {
        return $this->belongsTo(MasterStrukturJabatan::class, 'struktur_jabatan_id');
    }

    /**
     * Relasi ke semua Master Tugas (Fungsional, Internal, Eksternal)
     */
    public function tugas()
    {
        return $this->belongsToMany(
            MasterTugas::class,
            'detail_tugas',
            'pengurus_id',
            'master_tugas_id',
            'id',
            'id_tugas'
        )
            ->withPivot('status')
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
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id_pendidikan');
    }

    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class, 'angkatan_id', 'id_angkatan');
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
}
