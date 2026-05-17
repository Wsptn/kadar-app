<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penguruses', function (Blueprint $table) {
            $table->unsignedBigInteger('entitas_daerah_id')->nullable()->after('daerah_id');
        });

        // Map existing string data to the new ID
        $penguruses = DB::table('penguruses')->whereNotNull('entitas_daerah')->get();
        foreach ($penguruses as $pengurus) {
            $entitas = DB::table('entitas_daerahs')->where('nama_entitas', $pengurus->entitas_daerah)->first();
            if ($entitas) {
                DB::table('penguruses')->where('id', $pengurus->id)->update([
                    'entitas_daerah_id' => $entitas->id
                ]);
            }
        }

        // Now we can drop the old column and setup foreign key
        Schema::table('penguruses', function (Blueprint $table) {
            $table->dropColumn('entitas_daerah');
            
            // Add foreign key constraint
            $table->foreign('entitas_daerah_id')->references('id')->on('entitas_daerahs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penguruses', function (Blueprint $table) {
            $table->dropForeign(['entitas_daerah_id']);
            $table->string('entitas_daerah')->nullable()->after('daerah_id');
        });

        // Try to reverse map if possible
        $penguruses = DB::table('penguruses')->whereNotNull('entitas_daerah_id')->get();
        foreach ($penguruses as $pengurus) {
            $entitas = DB::table('entitas_daerahs')->where('id', $pengurus->entitas_daerah_id)->first();
            if ($entitas) {
                DB::table('penguruses')->where('id', $pengurus->id)->update([
                    'entitas_daerah' => $entitas->nama_entitas
                ]);
            }
        }

        Schema::table('penguruses', function (Blueprint $table) {
            $table->dropColumn('entitas_daerah_id');
        });
    }
};
