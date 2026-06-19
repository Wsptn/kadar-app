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
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatan_detail')->onDelete('set null')->after('pengurus_id');
            $table->foreignId('tugas_id')->nullable()->constrained('tugas_detail')->onDelete('set null')->after('jabatan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian_kinerja', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropColumn('jabatan_id');
            $table->dropForeign(['tugas_id']);
            $table->dropColumn('tugas_id');
        });
    }
};
