<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTugasInternal extends Model
{
    /// Nama tabel di database
    protected $table = 'master_tugas_internals';
    protected $primaryKey = 'id_internal';

    // Kolom yang boleh diisi (fillable)
    protected $fillable = [
        'internal',
        'keterangan'
    ];
}
