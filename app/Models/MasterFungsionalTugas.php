<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterFungsionalTugas extends Model
{
    // Nama tabel di database
    protected $table = 'master_fungsional_tugas';
    protected $primaryKey = 'id_tugas';

    // Kolom yang boleh diisi (fillable)
    protected $fillable = [
        'tugas',
        'keterangan'
    ];
}
