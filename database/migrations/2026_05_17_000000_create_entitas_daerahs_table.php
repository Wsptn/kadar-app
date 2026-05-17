<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entitas_daerahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_entitas');
            $table->timestamps();
        });

        // Insert default values provided by user
        $opsiEntitas = [
            'LPBA',
            'Idadiyah SLTP',
            'Teknologi',
            'BPK & Awwaliyah',
            'Pondok Mahasiswa (POMAS)',
            'SPThree (KIP)',
            'Bahasa',
            'MINM',
            'LIPS',
            'MAK',
            'MIPA SMP & SMA',
            'MIPA MANJ',
            'Diniyah',
            'Haddamiyah',
            'IPS',
            'Tahsin (PPIQ)',
            'Tahfidz (PPIQ)',
            'Awwaliyah',
            'Idadiyah SLTA',
        ];

        foreach ($opsiEntitas as $opsi) {
            DB::table('entitas_daerahs')->insert([
                'nama_entitas' => $opsi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entitas_daerahs');
    }
};
