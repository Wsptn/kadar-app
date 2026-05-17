<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Buat tabel kinerja_details
        Schema::create('kinerja_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kinerja_id');
            $table->unsignedBigInteger('instrumen_id');
            $table->integer('skor');
            $table->timestamps();

            $table->foreign('kinerja_id')->references('id')->on('kinerjas')->onDelete('cascade');
            $table->foreign('instrumen_id')->references('id')->on('master_instrumens')->onDelete('restrict');
        });

        // 2. Pindahkan data skor lama ke kinerja_details
        $kinerjas = DB::table('kinerjas')->get();
        foreach ($kinerjas as $kinerja) {
            // Map nama kolom lama ke ID instrumen yang baru dimasukkan di tabel master_instrumens
            $skorMap = [
                1 => $kinerja->skor_disiplin_waktu,
                2 => $kinerja->skor_tanggung_jawab_izin,
                3 => $kinerja->skor_selesai_tugas,
                4 => $kinerja->skor_loyalitas,
                5 => $kinerja->skor_akhlak,
                6 => $kinerja->skor_contoh,
                7 => $kinerja->skor_tupoksi,
                8 => $kinerja->skor_komunikasi,
                9 => $kinerja->skor_koordinasi,
                10 => $kinerja->skor_kebersamaan,
            ];

            foreach ($skorMap as $instrumenId => $skor) {
                if ($skor !== null) {
                    DB::table('kinerja_details')->insert([
                        'kinerja_id' => $kinerja->id,
                        'instrumen_id' => $instrumenId,
                        'skor' => $skor,
                        'created_at' => $kinerja->created_at,
                        'updated_at' => $kinerja->updated_at,
                    ]);
                }
            }
        }

        // 3. Hapus kolom skor lama di tabel kinerjas
        Schema::table('kinerjas', function (Blueprint $table) {
            $table->dropColumn([
                'skor_disiplin_waktu',
                'skor_tanggung_jawab_izin',
                'skor_selesai_tugas',
                'skor_loyalitas',
                'skor_akhlak',
                'skor_contoh',
                'skor_tupoksi',
                'skor_komunikasi',
                'skor_koordinasi',
                'skor_kebersamaan'
            ]);
        });
    }

    public function down()
    {
        // Reverse
        Schema::table('kinerjas', function (Blueprint $table) {
            $table->integer('skor_disiplin_waktu')->nullable();
            $table->integer('skor_tanggung_jawab_izin')->nullable();
            $table->integer('skor_selesai_tugas')->nullable();
            $table->integer('skor_loyalitas')->nullable();
            $table->integer('skor_akhlak')->nullable();
            $table->integer('skor_contoh')->nullable();
            $table->integer('skor_tupoksi')->nullable();
            $table->integer('skor_komunikasi')->nullable();
            $table->integer('skor_koordinasi')->nullable();
            $table->integer('skor_kebersamaan')->nullable();
        });

        // Kembalikan data (jika diperlukan)
        // ... (dilewati untuk penyederhanaan)

        Schema::dropIfExists('kinerja_details');
    }
};
