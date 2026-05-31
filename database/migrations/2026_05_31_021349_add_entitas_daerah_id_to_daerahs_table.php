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
        Schema::table('daerahs', function (Blueprint $table) {
            $table->foreignId('entitas_daerah_id')
                ->nullable()
                ->after('wilayah_id')
                ->constrained('entitas_daerahs')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daerahs', function (Blueprint $table) {
            $table->dropForeign(['entitas_daerah_id']);
            $table->dropColumn('entitas_daerah_id');
        });
    }
};
