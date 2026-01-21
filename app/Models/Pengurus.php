<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengurus extends Model
{
    protected $table = 'penguruses';

    protected $fillable = [
        'niup',
        'nama',
        'wilayah_id',
        'daerah_id',
        'entitas_daerah',
        'kamar_id',
        'entitas_id',
        'jabatan_id',
        'jenis_jabatan_id',
        'grade_jabatan_id',
        'sk_kepengurusan',
        // 'fungsional_tugas_id',  <-- SUDAH DIHAPUS (Karena pindah ke tabel pivot)
        'rangkap_internal_id',
        'rangkap_eksternal_id',
        'status',
        'pendidikan_id',
        'angkatan_id',

        // berkas / file
        'berkas_sk_pengurus',
        'berkas_surat_tugas',
        'berkas_plt',
        'berkas_lain',
        'foto',
    ];

    // === RELATIONS ===

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function daerah()
    {
        return $this->belongsTo(Daerah::class, 'daerah_id');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function entitas()
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function jenisJabatan()
    {
        return $this->belongsTo(JenisJabatan::class, 'jenis_jabatan_id');
    }

    public function gradeJabatan()
    {
        return $this->belongsTo(GradeJabatan::class, 'grade_jabatan_id');
    }

    /**
     * PERBAIKAN UTAMA DI SINI
     * Mengubah relasi menjadi Many-to-Many ke MasterFungsionalTugas
     */
    public function fungsionalTugas()
    {
        return $this->belongsToMany(
            MasterFungsionalTugas::class,
            'pengurus_fungsional_tugas',
            'pengurus_id',
            'master_fungsional_tugas_id',
            'id',
            'id_tugas'
        )
            ->withPivot('status')
            ->withTimestamps();
    }

    public function rangkapInternal()
    {
        // Pastikan nama kolom PK di MasterTugasInternal benar 'id_internal'
        return $this->belongsTo(MasterTugasInternal::class, 'rangkap_internal_id', 'id_internal');
    }

    public function rangkapEksternal()
    {
        // Pastikan nama kolom PK di MasterTugasEksternal benar 'id_eksternal'
        return $this->belongsTo(MasterTugasEksternal::class, 'rangkap_eksternal_id', 'id_eksternal');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id_pendidikan');
    }

    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class, 'angkatan_id', 'id_angkatan');
    }
}
