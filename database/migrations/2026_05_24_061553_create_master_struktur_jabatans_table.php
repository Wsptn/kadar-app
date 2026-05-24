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
        Schema::create('master_struktur_jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('entitas')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('jenis_jabatan')->nullable();
            $table->string('grade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_struktur_jabatans');
    }
};
