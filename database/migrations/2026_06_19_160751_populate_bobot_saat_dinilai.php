<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Isi nilai bobot historis pada data lama yang nilainya masih NULL
        DB::statement("
            UPDATE detail_penilaian_kinerja dpk
            JOIN instrumen i ON dpk.instrumen_id = i.id
            SET dpk.bobot_saat_dinilai = i.bobot
            WHERE dpk.bobot_saat_dinilai IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kita tidak boleh mengubahnya kembali menjadi NULL karena itu akan menghapus jejak
    }
};
