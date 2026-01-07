<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Angkatan extends Model
{
    /// Nama tabel di database
    protected $table = 'angkatans';
    protected $primaryKey = 'id_angkatan';

    // Kolom yang boleh diisi (fillable)
    protected $fillable = [
        'angkatan',
        'keterangan'
    ];
}
