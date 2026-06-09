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
        // 1. Hapus kolom angkatan_id dari tabel penguruses jika ada
        if (Schema::hasColumn('penguruses', 'angkatan_id')) {
            Schema::table('penguruses', function (Blueprint $table) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign(['angkatan_id']);
                }
                $table->dropColumn('angkatan_id');
            });
        }

        // 2. Hapus tabel angkatans jika ada
        if (Schema::hasTable('angkatans')) {
            Schema::dropIfExists('angkatans');
        }

        // 3. Kembalikan struktur Wilayah -> Daerah -> Kamar, hapus Domisili
        if (Schema::hasTable('domisilis')) {
            if (Schema::hasColumn('penguruses', 'domisili_id')) {
                Schema::table('penguruses', function (Blueprint $table) {
                    if (DB::getDriverName() !== 'sqlite') {
                        $table->dropForeign(['domisili_id']);
                    }
                    $table->dropColumn('domisili_id');
                });
            }
            Schema::dropIfExists('domisilis');
        }

        if (!Schema::hasTable('wilayahs')) {
            Schema::create('wilayahs', function (Blueprint $table) {
                $table->id();
                $table->string('nama_wilayah');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('daerahs')) {
            Schema::create('daerahs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wilayah_id')->constrained('wilayahs')->onDelete('cascade');
                $table->string('nama_daerah');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kamars')) {
            Schema::create('kamars', function (Blueprint $table) {
                $table->id();
                $table->foreignId('daerah_id')->constrained('daerahs')->onDelete('cascade');
                $table->string('nomor_kamar');
                $table->timestamps();
            });
        }

        // Tambahkan kamar_id kembali ke penguruses
        if (!Schema::hasColumn('penguruses', 'kamar_id')) {
            Schema::table('penguruses', function (Blueprint $table) {
                $table->foreignId('kamar_id')->nullable()->constrained('kamars')->onDelete('set null')->after('id');
            });
        }

        // 4. Rename dan Sesuaikan tabel detail agar sesuai dengan ERD

        // riwayat_tugas -> tugas_detail
        if (Schema::hasTable('riwayat_tugas') && !Schema::hasTable('tugas_detail')) {
            Schema::rename('riwayat_tugas', 'tugas_detail');
        }

        // riwayat_jabatans -> jabatan_detail
        if (Schema::hasTable('riwayat_jabatans') && !Schema::hasTable('jabatan_detail')) {
            Schema::rename('riwayat_jabatans', 'jabatan_detail');
        }

        // Pendidikan (bikin baru tabel pendidikan_detail)
        if (!Schema::hasTable('pendidikan_detail')) {
            Schema::create('pendidikan_detail', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pengurus_id')->constrained('penguruses')->onDelete('cascade');
                $table->unsignedBigInteger('pendidikan_id');
                $table->foreign('pendidikan_id')->references('id_pendidikan')->on('pendidikans')->onDelete('cascade');
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
                $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
                $table->timestamps();
            });
        }

        // Penilaian Kinerja (bikin baru detail_penilaian_kinerja yang direct ke pengurus)
        if (!Schema::hasTable('detail_penilaian_kinerja')) {
            Schema::create('detail_penilaian_kinerja', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pengurus_id')->constrained('penguruses')->onDelete('cascade');
                $table->foreignId('instrumen_id')->constrained('master_instrumens')->onDelete('cascade');
                $table->integer('skor')->nullable();
                $table->date('tanggal_penilaian')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrasi down dibiarkan kosong agar tidak secara tidak sengaja merusak database
        // pada proses rollback di perombakan besar ini.
    }
};
