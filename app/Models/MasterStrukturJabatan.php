<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterStrukturJabatan extends Model
{
    protected $table = 'master_struktur_jabatans';

    protected $fillable = [
        'entitas',
        'jabatan',
        'jenis_jabatan',
        'grade',
    ];

    public function penguruses()
    {
        return $this->hasMany(Pengurus::class, 'struktur_jabatan_id');
    }
}
