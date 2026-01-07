<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // 1. Cek & Tambah Username
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('name');
            }

            // 2. Cek & Tambah Level
            if (!Schema::hasColumn('users', 'level')) {
                $table->enum('level', ['Admin', 'Biktren', 'Wilayah', 'Daerah'])->after('password');
            }

            // 3. Cek & Tambah Status
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('level');
            }

            // 4. Cek & Tambah Wilayah & Daerah
            if (!Schema::hasColumn('users', 'wilayah_id')) {
                $table->unsignedBigInteger('wilayah_id')->nullable()->after('status');
            }

            if (!Schema::hasColumn('users', 'daerah_id')) {
                $table->unsignedBigInteger('daerah_id')->nullable()->after('wilayah_id');
            }

            // 5. INI PERBAIKANNYA (Menambah Kolom Foto)
            if (!Schema::hasColumn('users', 'foto')) {
                $table->string('foto')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Biarkan kosong agar aman saat rollback (sesuai request sebelumnya)
        });
    }
};
