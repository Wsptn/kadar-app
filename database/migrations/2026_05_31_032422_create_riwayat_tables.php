<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom tgl_mulai_tugas di tabel penguruses
        Schema::table('penguruses', function (Blueprint $table) {
            $table->date('tgl_mulai_tugas')->nullable()->after('status');
        });

        // 2. Buat tabel riwayat_jabatans
        Schema::create('riwayat_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengurus_id')->constrained('penguruses')->onDelete('cascade');
            $table->unsignedBigInteger('struktur_jabatan_id');
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->timestamps();
        });

        // 3. Buat tabel riwayat_tugas
        Schema::create('riwayat_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengurus_id')->constrained('penguruses')->onDelete('cascade');
            $table->unsignedBigInteger('master_tugas_id');
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_tugas');
        Schema::dropIfExists('riwayat_jabatans');
        Schema::table('penguruses', function (Blueprint $table) {
            $table->dropColumn('tgl_mulai_tugas');
        });
    }
};
