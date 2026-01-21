<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pengurus_fungsional_tugas', function (Blueprint $table) {
            $table->id();

            // Relasi ke Pengurus (Default 'id' aman)
            $table->foreignId('pengurus_id')->constrained('penguruses')->onDelete('cascade');

            $table->unsignedBigInteger('master_fungsional_tugas_id');

            $table->foreign('master_fungsional_tugas_id')
                ->references('id_tugas')
                ->on('master_fungsional_tugas')
                ->onDelete('cascade');

            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengurus_fungsional_tugas');
    }
};
