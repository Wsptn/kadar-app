<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create Master Tugas table
        Schema::create('master_tugas', function (Blueprint $table) {
            $table->id('id_tugas');
            $table->string('nama_tugas');
            $table->enum('jenis_tugas', ['fungsional', 'internal', 'eksternal']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Create Pengurus Tugas pivot table
        Schema::create('pengurus_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengurus_id')->constrained('penguruses')->onDelete('cascade');
            $table->unsignedBigInteger('master_tugas_id');
            $table->foreign('master_tugas_id')->references('id_tugas')->on('master_tugas')->onDelete('cascade');
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->timestamps();
        });

        // 3. Migrate data
        $mapFungsional = [];
        if (Schema::hasTable('master_fungsional_tugas')) {
            $fungsionalList = DB::table('master_fungsional_tugas')->get();
            foreach ($fungsionalList as $item) {
                $newId = DB::table('master_tugas')->insertGetId([
                    'nama_tugas' => $item->tugas,
                    'jenis_tugas' => 'fungsional',
                    'keterangan' => $item->keterangan,
                    'created_at' => $item->created_at ?? now(),
                    'updated_at' => $item->updated_at ?? now(),
                ]);
                $mapFungsional[$item->id_tugas] = $newId;
            }
        }

        $mapInternal = [];
        if (Schema::hasTable('master_tugas_internals')) {
            $internalList = DB::table('master_tugas_internals')->get();
            foreach ($internalList as $item) {
                $newId = DB::table('master_tugas')->insertGetId([
                    'nama_tugas' => $item->internal,
                    'jenis_tugas' => 'internal',
                    'keterangan' => $item->keterangan,
                    'created_at' => $item->created_at ?? now(),
                    'updated_at' => $item->updated_at ?? now(),
                ]);
                $mapInternal[$item->id_internal] = $newId;
            }
        }

        $mapEksternal = [];
        if (Schema::hasTable('master_tugas_eksternals')) {
            $eksternalList = DB::table('master_tugas_eksternals')->get();
            foreach ($eksternalList as $item) {
                $newId = DB::table('master_tugas')->insertGetId([
                    'nama_tugas' => $item->eksternal,
                    'jenis_tugas' => 'eksternal',
                    'keterangan' => $item->keterangan,
                    'created_at' => $item->created_at ?? now(),
                    'updated_at' => $item->updated_at ?? now(),
                ]);
                $mapEksternal[$item->id_eksternal] = $newId;
            }
        }

        // Migrate Pivot Fungsional
        if (Schema::hasTable('pengurus_fungsional_tugas')) {
            $pivotFungsional = DB::table('pengurus_fungsional_tugas')->get();
            foreach ($pivotFungsional as $pivot) {
                if (isset($mapFungsional[$pivot->master_fungsional_tugas_id])) {
                    DB::table('pengurus_tugas')->insert([
                        'pengurus_id' => $pivot->pengurus_id,
                        'master_tugas_id' => $mapFungsional[$pivot->master_fungsional_tugas_id],
                        'status' => $pivot->status,
                        'created_at' => $pivot->created_at ?? now(),
                        'updated_at' => $pivot->updated_at ?? now(),
                    ]);
                }
            }
        }

        // Migrate Internal & Eksternal from penguruses
        $penguruses = DB::table('penguruses')->get();
        foreach ($penguruses as $p) {
            if (property_exists($p, 'rangkap_internal_id') && $p->rangkap_internal_id && isset($mapInternal[$p->rangkap_internal_id])) {
                DB::table('pengurus_tugas')->insert([
                    'pengurus_id' => $p->id,
                    'master_tugas_id' => $mapInternal[$p->rangkap_internal_id],
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if (property_exists($p, 'rangkap_eksternal_id') && $p->rangkap_eksternal_id && isset($mapEksternal[$p->rangkap_eksternal_id])) {
                DB::table('pengurus_tugas')->insert([
                    'pengurus_id' => $p->id,
                    'master_tugas_id' => $mapEksternal[$p->rangkap_eksternal_id],
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 4. Drop old foreign keys and columns on penguruses
        Schema::table('penguruses', function (Blueprint $table) {
            if (Schema::hasColumn('penguruses', 'fungsional_tugas_id')) {
                $table->dropForeign(['fungsional_tugas_id']);
                $table->dropColumn(['fungsional_tugas_id']);
            }
            if (Schema::hasColumn('penguruses', 'rangkap_internal_id')) {
                $table->dropForeign(['rangkap_internal_id']);
                $table->dropColumn(['rangkap_internal_id']);
            }
            if (Schema::hasColumn('penguruses', 'rangkap_eksternal_id')) {
                $table->dropForeign(['rangkap_eksternal_id']);
                $table->dropColumn(['rangkap_eksternal_id']);
            }
        });

        // 5. Drop old tables
        Schema::dropIfExists('pengurus_fungsional_tugas');
        Schema::dropIfExists('master_fungsional_tugas');
        Schema::dropIfExists('master_tugas_internals');
        Schema::dropIfExists('master_tugas_eksternals');
    }

    public function down(): void
    {
        // Reverting this is complex, leaving blank to enforce one-way migration
    }
};
