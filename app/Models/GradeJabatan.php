<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeJabatan extends Model
{
    protected $table = 'grade_jabatans';

    protected $fillable = [
        'entitas_id',
        'jabatan_id',
        'jenis_jabatan_id',
        'grade',
        'keterangan'
    ];

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
    public function jenis()
    {
        return $this->belongsTo(JenisJabatan::class, 'jenis_jabatan_id');
    }
}
