<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterStrukturJabatan extends Model
{
    protected $table = 'jabatan';

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
