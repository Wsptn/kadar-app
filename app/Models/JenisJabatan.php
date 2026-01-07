<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisJabatan extends Model
{
    protected $table = 'jenis_jabatans';

    protected $fillable = ['entitas_id', 'jabatan_id', 'jenis_jabatan'];

    public function entitas()
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function grade()
    {
        return $this->hasMany(GradeJabatan::class, 'jenis_jabatan_id');
    }
}
