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
        Schema::create('penguruses', function (Blueprint $table) {
            $table->id();

            // Data dasar
            $table->string('niup')->unique();
            $table->string('nama');

            // ==========================
            //   WILAYAH → DAERAH → KAMAR
            // ==========================
            $table->foreignId('wilayah_id')->nullable()
                ->constrained('wilayahs')->nullOnDelete();

            $table->foreignId('daerah_id')->nullable()
                ->constrained('daerahs')->nullOnDelete();

            $table->foreignId('kamar_id')->nullable()
                ->constrained('kamars')->nullOnDelete();

            // ==========================
            //   JABATAN
            // ==========================
            $table->foreignId('entitas_id')->nullable()
                ->constrained('entitas')->nullOnDelete();

            $table->foreignId('jabatan_id')->nullable()
                ->constrained('jabatans')->nullOnDelete();

            $table->foreignId('jenis_jabatan_id')->nullable()
                ->constrained('jenis_jabatans')->nullOnDelete();

            $table->foreignId('grade_jabatan_id')->nullable()
                ->constrained('grade_jabatans')->nullOnDelete();

            // ==========================
            //   Fungsional & Rangkap Tugas
            // ==========================

            // master_fungsional_tugas → PK = id_tugas
            $table->unsignedBigInteger('fungsional_tugas_id')->nullable();
            $table->foreign('fungsional_tugas_id')
                ->references('id_tugas')
                ->on('master_fungsional_tugas')
                ->nullOnDelete();

            // master_tugas_internals → PK = id_internal
            $table->unsignedBigInteger('rangkap_internal_id')->nullable();
            $table->foreign('rangkap_internal_id')
                ->references('id_internal')
                ->on('master_tugas_internals')
                ->nullOnDelete();

            // master_tugas_eksternals → PK = id_eksternal
            $table->unsignedBigInteger('rangkap_eksternal_id')->nullable();
            $table->foreign('rangkap_eksternal_id')
                ->references('id_eksternal')
                ->on('master_tugas_eksternals')
                ->nullOnDelete();

            // ==========================
            //   PENDIDIKAN & ANGKATAN
            // ==========================

            // pendidikans → PK = id_pendidikan
            $table->unsignedBigInteger('pendidikan_id')->nullable();
            $table->foreign('pendidikan_id')
                ->references('id_pendidikan')
                ->on('pendidikans')
                ->nullOnDelete();

            // angkatans → PK = id_angkatan
            $table->unsignedBigInteger('angkatan_id')->nullable();
            $table->foreign('angkatan_id')
                ->references('id_angkatan')
                ->on('angkatans')
                ->nullOnDelete();

            // ==========================
            //   FILE & BERKAS
            // ==========================
            $table->string('sk_kepengurusan')->nullable();
            $table->string('berkas_sk_pengurus')->nullable();
            $table->string('berkas_surat_tugas')->nullable();
            $table->string('berkas_plt')->nullable();
            $table->string('berkas_lain')->nullable();
            $table->string('foto')->nullable();

            // STATUS
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penguruses');
    }
};
