<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entitas extends Model
{
    protected $table = 'entitas';

    protected $fillable = ['nama_entitas'];

    public function jabatan()
    {
        return $this->hasMany(Jabatan::class, 'entitas_id');
    }

    public function jenisJabatan()
    {
        return $this->hasMany(JenisJabatan::class, 'entitas_id');
    }

    public function gradeJabatan()
    {
        return $this->hasMany(GradeJabatan::class, 'entitas_id');
    }
}
