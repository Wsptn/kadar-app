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
        Schema::dropIfExists('wali_asuh');
        Schema::dropIfExists('pengajar');
        Schema::dropIfExists('muallim');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not recreating tables as they are obsolete
    }
};
