<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengurus_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_penilaian');

            $table->integer('skor_disiplin_waktu');       // Bobot 13%
            $table->integer('skor_tanggung_jawab_izin');  // Bobot 11%
            $table->integer('skor_selesai_tugas');        // Bobot 12%
            $table->integer('skor_loyalitas');            // Bobot 8%
            $table->integer('skor_akhlak');               // Bobot 14%
            $table->integer('skor_contoh');               // Bobot 12%
            $table->integer('skor_tupoksi');              // Bobot 11%
            $table->integer('skor_komunikasi');           // Bobot 7%
            $table->integer('skor_koordinasi');           // Bobot 7%
            $table->integer('skor_kebersamaan');          // Bobot 5%

            // === HASIL AKHIR ===
            $table->decimal('nilai_total', 5, 2);
            $table->string('huruf_mutu', 2);      // A, B, C, D, E

            $table->text('rekomendasi');

            $table->text('catatan')->nullable();
            $table->enum('status_tindak_lanjut', ['belum', 'sudah'])->default('belum');
            $table->timestamp('tanggal_tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kinerjas');
    }
};
