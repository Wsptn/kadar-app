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
            $table->foreignId('pengurus_id')->constrained()->onDelete('cascade'); // Relasi ke Pengurus
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
            $table->decimal('nilai_total', 5, 2); // Hasil perhitungan bobot
            $table->string('huruf_mutu', 2);      // A, B, C, D, E

            $table->enum('rekomendasi', ['Kinerja Memuaskan', 'Pendampingan', 'Pembinaan']);

            $table->text('catatan')->nullable(); // Uraian tanggapan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kinerjas');
    }
};
