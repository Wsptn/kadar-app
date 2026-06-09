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
        $renames = [
            'wilayahs' => 'wilayah',
            'daerahs' => 'daerah',
            'kamars' => 'kamar',
            'penguruses' => 'pengurus',
            'pendidikans' => 'pendidikan',
            'master_tugas' => 'tugas',
            'master_struktur_jabatans' => 'jabatan',
            'master_instrumens' => 'instrumen',
        ];

        foreach ($renames as $old => $new) {
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }

        // Handle leftovers
        if (Schema::hasTable('detail_tugas')) {
            Schema::dropIfExists('detail_tugas');
        }

        // Handle Kinerja: rename kinerjas to penilaian_kinerja
        if (Schema::hasTable('kinerjas') && !Schema::hasTable('penilaian_kinerja')) {
            Schema::rename('kinerjas', 'penilaian_kinerja');
        }

        // Drop empty detail_penilaian_kinerja that was just created if kinerja_details has data
        if (Schema::hasTable('detail_penilaian_kinerja') && Schema::hasTable('kinerja_details')) {
            Schema::dropIfExists('detail_penilaian_kinerja');
            Schema::rename('kinerja_details', 'detail_penilaian_kinerja');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
