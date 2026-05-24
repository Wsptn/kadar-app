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
        Schema::rename('pengurus_tugas', 'detail_tugas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('detail_tugas', 'pengurus_tugas');
    }
};
