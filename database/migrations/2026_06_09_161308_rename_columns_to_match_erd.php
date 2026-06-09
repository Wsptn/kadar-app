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
        Schema::table('pengurus', function (Blueprint $table) {
            if (Schema::hasColumn('pengurus', 'struktur_jabatan_id')) {
                $table->renameColumn('struktur_jabatan_id', 'jabatan_id');
            }
        });

        Schema::table('jabatan_detail', function (Blueprint $table) {
            if (Schema::hasColumn('jabatan_detail', 'struktur_jabatan_id')) {
                $table->renameColumn('struktur_jabatan_id', 'jabatan_id');
            }
        });

        Schema::table('tugas_detail', function (Blueprint $table) {
            if (Schema::hasColumn('tugas_detail', 'master_tugas_id')) {
                $table->renameColumn('master_tugas_id', 'tugas_id');
            }
        });

        Schema::table('tugas', function (Blueprint $table) {
            if (Schema::hasColumn('tugas', 'id_tugas')) {
                $table->renameColumn('id_tugas', 'id');
            }
        });

        Schema::table('pendidikan', function (Blueprint $table) {
            if (Schema::hasColumn('pendidikan', 'id_pendidikan')) {
                $table->renameColumn('id_pendidikan', 'id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implemented
    }
};
