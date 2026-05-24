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
        Schema::table('master_tugas', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });

        Schema::table('pendidikans', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });

        Schema::table('angkatans', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_tugas', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });

        Schema::table('pendidikans', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });

        Schema::table('angkatans', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
    }
};
