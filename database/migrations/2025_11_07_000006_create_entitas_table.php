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
        Schema::create('entitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_entitas', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entitas');
    }
};
