<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatTugas extends Model
{
    protected $table = 'tugas_detail';

    protected $fillable = [
        'pengurus_id',
        'tugas_id',
        'tgl_mulai',
        'tgl_selesai',
        'status',
    ];

    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }

    public function masterTugas()
    {
        return $this->belongsTo(MasterTugas::class, 'tugas_id', 'id');
    }
}
