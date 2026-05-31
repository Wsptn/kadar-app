<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domisili extends Model
{
    use HasFactory;

    protected $table = 'domisilis';

    protected $fillable = [
        'wilayah',
        'daerah',
        'entitas_daerah',
        'kamar',
    ];
}
