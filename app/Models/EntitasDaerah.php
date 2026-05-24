<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntitasDaerah extends Model
{
    protected $table = 'entitas_daerahs';

    protected $fillable = [
        'nama_entitas_daerah',
    ];
}
