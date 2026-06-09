<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Daerah extends Model
{
    protected $table = 'daerah';
    protected $fillable = ['nama_daerah', 'wilayah_id', 'entitas_daerah'];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function kamars()
    {
        return $this->hasMany(Kamar::class, 'daerah_id');
    }
}
