<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $table = 'kamar';
    protected $fillable = ['nomor_kamar', 'daerah_id'];

    public function daerah()
    {
        return $this->belongsTo(Daerah::class, 'daerah_id');
    }

    public function penguruses()
    {
        return $this->hasMany(Pengurus::class, 'kamar_id');
    }
}
