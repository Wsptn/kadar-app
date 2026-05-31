<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('wilayah')->nullable()->after('wilayah_id');
            $table->string('daerah')->nullable()->after('daerah_id');
        });

        // 2. Update penguruses table
        Schema::table('penguruses', function (Blueprint $table) {
            $table->foreignId('domisili_id')->nullable()->constrained('domisilis')->onDelete('set null')->after('kamar_id');
            $table->string('entitas_daerah')->nullable()->after('entitas_daerah_id');
        });

        // 3. Migrate Data
        
        // A. Migrate Kamar -> Domisili
        $kamars = DB::table('kamars')
            ->join('daerahs', 'kamars.daerah_id', '=', 'daerahs.id')
            ->join('wilayahs', 'daerahs.wilayah_id', '=', 'wilayahs.id')
            ->leftJoin('entitas_daerahs', 'daerahs.entitas_daerah_id', '=', 'entitas_daerahs.id')
            ->select(
                'kamars.id as kamar_id',
                'kamars.nomor_kamar as kamar',
                'daerahs.id as daerah_id',
                'daerahs.nama_daerah as daerah',
                'wilayahs.id as wilayah_id',
                'wilayahs.nama_wilayah as wilayah',
                'entitas_daerahs.nama_entitas_daerah as entitas_daerah'
            )
            ->get();

        $kamarToDomisili = [];
        foreach ($kamars as $k) {
            // Cek jika sudah ada
            $existing = DB::table('domisilis')->where([
                'wilayah' => $k->wilayah,
                'daerah' => $k->daerah,
                'entitas_daerah' => $k->entitas_daerah,
                'kamar' => $k->kamar,
            ])->first();
            
            if ($existing) {
                $kamarToDomisili[$k->kamar_id] = $existing->id;
            } else {
                $newId = DB::table('domisilis')->insertGetId([
                    'wilayah' => $k->wilayah,
                    'daerah' => $k->daerah,
                    'entitas_daerah' => $k->entitas_daerah,
                    'kamar' => $k->kamar,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $kamarToDomisili[$k->kamar_id] = $newId;
            }
        }

        // B. Migrate Users
        $users = DB::table('users')->get();
        foreach ($users as $u) {
            $w = DB::table('wilayahs')->where('id', $u->wilayah_id)->first();
            $d = DB::table('daerahs')->where('id', $u->daerah_id)->first();
            DB::table('users')->where('id', $u->id)->update([
                'wilayah' => $w ? $w->nama_wilayah : null,
                'daerah' => $d ? $d->nama_daerah : null,
            ]);
        }

        // C. Migrate Penguruses
        $penguruses = DB::table('penguruses')->get();
        foreach ($penguruses as $p) {
            $entitasString = null;
            if ($p->entitas_daerah_id) {
                $e = DB::table('entitas_daerahs')->where('id', $p->entitas_daerah_id)->first();
                if ($e) $entitasString = $e->nama_entitas_daerah;
            }

            DB::table('penguruses')->where('id', $p->id)->update([
                'domisili_id' => $p->kamar_id ? ($kamarToDomisili[$p->kamar_id] ?? null) : null,
                'entitas_daerah' => $entitasString,
            ]);
        }

        // 4. Drop Old Foreign Keys and Columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('wilayah_id');
            $table->dropColumn('daerah_id');
        });

        Schema::table('penguruses', function (Blueprint $table) {
            // Drop foreign keys if they exist. In SQLite this might not work perfectly, but for MySQL it's fine.
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['wilayah_id']);
                $table->dropForeign(['daerah_id']);
                $table->dropForeign(['kamar_id']);
                $table->dropForeign(['entitas_daerah_id']);
            }
            $table->dropColumn('wilayah_id');
            $table->dropColumn('daerah_id');
            $table->dropColumn('kamar_id');
            $table->dropColumn('entitas_daerah_id');
        });

        // 5. Drop Old Tables
        Schema::dropIfExists('kamars');
        
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('daerahs', function (Blueprint $table) {
                $table->dropForeign(['entitas_daerah_id']);
            });
        }
        Schema::dropIfExists('daerahs');
        Schema::dropIfExists('wilayahs');
        Schema::dropIfExists('entitas_daerahs');
    }

    public function down(): void
    {
        throw new \Exception('Irreversible migration. Cannot rollback.');
    }
};
