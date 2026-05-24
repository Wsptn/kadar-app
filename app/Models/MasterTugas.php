<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTugas extends Model
{
    protected $table = 'master_tugas';
    protected $primaryKey = 'id_tugas';

    protected $fillable = [
        'nama_tugas',
        'jenis_tugas', // enum: 'fungsional', 'internal', 'eksternal'
    ];

    public function pengurus()
    {
        return $this->belongsToMany(
            Pengurus::class,
            'detail_tugas',
            'master_tugas_id',
            'pengurus_id',
            'id_tugas',
            'id'
        )
        ->withPivot('status')
        ->withTimestamps();
    }
}
