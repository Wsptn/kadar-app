<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamars';
    protected $fillable = ['wilayah_id', 'daerah_id', 'nomor_kamar'];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'id');
    }

    public function daerah()
    {
        return $this->belongsTo(Daerah::class, 'daerah_id');
    }
}
