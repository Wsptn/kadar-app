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
        Schema::table('penilaian_kinerja', function (Blueprint $table) {
            $table->dropForeign('kinerjas_pengurus_id_foreign');
            $table->foreign('pengurus_id')->references('id')->on('pengurus')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian_kinerja', function (Blueprint $table) {
            $table->dropForeign(['pengurus_id']);
            $table->foreign('pengurus_id')->references('id')->on('pengurus')->onDelete('cascade');
        });
    }
};
