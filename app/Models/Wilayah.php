<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;
    protected $table = 'wilayahs';
    protected $fillable = ['nama_wilayah'];

    public function daerah()
    {
        return $this->hasMany(Daerah::class);
    }
}
