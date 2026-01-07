<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTugasEksternal extends Model
{
    /// Nama tabel di database
    protected $table = 'master_tugas_eksternals';
    protected $primaryKey = 'id_eksternal';

    // Kolom yang boleh diisi (fillable)
    protected $fillable = [
        'eksternal',
        'keterangan'
    ];
}
