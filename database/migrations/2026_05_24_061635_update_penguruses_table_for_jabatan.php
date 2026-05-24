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
        Schema::table('penguruses', function (Blueprint $table) {
            $table->dropForeign(['entitas_id']);
            $table->dropForeign(['jabatan_id']);
            $table->dropForeign(['jenis_jabatan_id']);
            $table->dropForeign(['grade_jabatan_id']);
            $table->dropColumn(['entitas_id', 'jabatan_id', 'jenis_jabatan_id', 'grade_jabatan_id']);
            $table->unsignedBigInteger('struktur_jabatan_id')->nullable()->after('kamar_id');
        });

        Schema::dropIfExists('grade_jabatans');
        Schema::dropIfExists('jenis_jabatans');
        Schema::dropIfExists('jabatans');
        Schema::dropIfExists('entitas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('entitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_entitas');
            $table->timestamps();
        });
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entitas_id');
            $table->string('nama_jabatan');
            $table->timestamps();
        });
        Schema::create('jenis_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entitas_id');
            $table->foreignId('jabatan_id');
            $table->string('jenis_jabatan');
            $table->timestamps();
        });
        Schema::create('grade_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entitas_id');
            $table->foreignId('jabatan_id');
            $table->foreignId('jenis_jabatan_id');
            $table->string('grade');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::table('penguruses', function (Blueprint $table) {
            $table->dropColumn('struktur_jabatan_id');
            $table->unsignedBigInteger('entitas_id')->nullable();
            $table->unsignedBigInteger('jabatan_id')->nullable();
            $table->unsignedBigInteger('jenis_jabatan_id')->nullable();
            $table->unsignedBigInteger('grade_jabatan_id')->nullable();
        });
    }
};
