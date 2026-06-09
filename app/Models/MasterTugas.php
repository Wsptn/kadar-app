<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTugas extends Model
{
    protected $table = 'tugas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_tugas',
        'jenis_tugas', // enum: 'fungsional', 'internal', 'eksternal'
    ];

    public function pengurus()
    {
        return $this->belongsToMany(
            Pengurus::class,
            'tugas_detail',
            'tugas_id',
            'pengurus_id',
            'id',
            'id'
        )
        ->withPivot('status')
        ->withTimestamps();
    }
}
