<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajar', function (Blueprint $table) {
            if (Schema::hasColumn('pengajar', 'mata_pelajaran')) {
                $table->dropColumn('mata_pelajaran');
            }
        });

        Schema::table('wali_asuh', function (Blueprint $table) {
            if (Schema::hasColumn('wali_asuh', 'wilayah')) {
                $table->dropColumn('wilayah');
            }
        });

        Schema::table('muallim', function (Blueprint $table) {
            if (Schema::hasColumn('muallim', 'bidang')) {
                $table->dropColumn('bidang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajar', function (Blueprint $table) {
            $table->string('mata_pelajaran')->nullable();
        });

        Schema::table('wali_asuh', function (Blueprint $table) {
            $table->string('wilayah')->nullable();
        });

        Schema::table('muallim', function (Blueprint $table) {
            $table->string('bidang')->nullable();
        });
    }
};
