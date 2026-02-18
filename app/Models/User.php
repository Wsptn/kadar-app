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
        'wilayah_id',
        'daerah_id',
        'jabatan_id',
        'entitas_id',
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

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function daerah()
    {
        return $this->belongsTo(Daerah::class);
    }

    // 1. Relasi Jabatan 
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    // 2. Relasi Kinerja (Untuk cek history nilai)
    public function kinerja()
    {
        return $this->hasMany(Kinerja::class);
    }

    // 3. Relasi Entitas (Untuk filter)
    public function entitas()
    {
        return $this->belongsTo(Entitas::class);
    }
}
