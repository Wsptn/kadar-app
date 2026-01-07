<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatans';

    protected $fillable = ['entitas_id', 'nama_jabatan'];

    public function entitas()
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    public function jenis()
    {
        return $this->hasMany(JenisJabatan::class, 'jabatan_id');
    }

    public function grade()
    {
        return $this->hasMany(GradeJabatan::class, 'jabatan_id');
    }
}
