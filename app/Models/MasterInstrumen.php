<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterInstrumen extends Model
{
    use HasFactory;

    protected $table = 'instrumen';
    protected $guarded = [];

    public function kinerjaDetails()
    {
        return $this->hasMany(KinerjaDetail::class, 'instrumen_id');
    }
}
