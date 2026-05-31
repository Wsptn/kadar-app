<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'level',
        'status',
        'wilayah',
        'daerah',
        'niup',
        'foto',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];

    // === HELPER METHOD LEVEL USER ===
    public function isAdmin()
    {
        return $this->level === 'Admin';
    }

    public function isBiktren()
    {
        return $this->level === 'Biktren';
    }

    public function isWilayah()
    {
        return $this->level === 'Wilayah';
    }

    public function isDaerah()
    {
        return $this->level === 'Daerah';
    }

    // === RELASI ANTAR TABEL (Wajib Ada) ===

    // 2. Relasi Kinerja (Untuk cek history nilai)
    public function kinerja()
    {
        return $this->hasMany(Kinerja::class);
    }
}
