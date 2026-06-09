<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikan extends Model
{
    protected $table = 'pendidikan_detail';

    protected $fillable = [
        'pengurus_id',
        'pendidikan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id');
    }
}
