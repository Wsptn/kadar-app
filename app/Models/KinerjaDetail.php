<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KinerjaDetail extends Model
{
    use HasFactory;

    protected $table = 'detail_penilaian_kinerja';
    protected $guarded = [];

    public function kinerja()
    {
        return $this->belongsTo(Kinerja::class, 'kinerja_id');
    }

    public function instrumen()
    {
        return $this->belongsTo(MasterInstrumen::class, 'instrumen_id');
    }
}
