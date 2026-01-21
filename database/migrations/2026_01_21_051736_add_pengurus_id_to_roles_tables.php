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
        // 1. Tambah ke tabel 'muallim'
        if (Schema::hasTable('muallim')) {
            Schema::table('muallim', function (Blueprint $table) {
                // Cek dulu biar tidak error jika dijalankan ulang
                if (!Schema::hasColumn('muallim', 'pengurus_id')) {
                    $table->foreignId('pengurus_id')->nullable()->after('id')->constrained('penguruses')->onDelete('cascade');
                }
                // Tambahkan kolom status jika belum ada (sesuai error sebelumnya)
                if (!Schema::hasColumn('muallim', 'status')) {
                    $table->enum('status', ['aktif', 'non_aktif'])->default('aktif')->after('pengurus_id');
                }
            });
        }

        // 2. Tambah ke tabel 'pengajar'
        if (Schema::hasTable('pengajar')) {
            Schema::table('pengajar', function (Blueprint $table) {
                if (!Schema::hasColumn('pengajar', 'pengurus_id')) {
                    $table->foreignId('pengurus_id')->nullable()->after('id')->constrained('penguruses')->onDelete('cascade');
                }
                if (!Schema::hasColumn('pengajar', 'status')) {
                    $table->enum('status', ['aktif', 'non_aktif'])->default('aktif')->after('pengurus_id');
                }
            });
        }

        // 3. Tambah ke tabel 'wali_asuh' (Sesuaikan nama tabel Anda, misal: wali_asuhs atau wali_asuh)
        // Cek nama tabel di database Anda, biasanya 'wali_asuh' atau 'wali_asuhs'
        $tabelWali = Schema::hasTable('wali_asuhs') ? 'wali_asuhs' : (Schema::hasTable('wali_asuh') ? 'wali_asuh' : null);

        if ($tabelWali) {
            Schema::table($tabelWali, function (Blueprint $table) use ($tabelWali) {
                if (!Schema::hasColumn($tabelWali, 'pengurus_id')) {
                    $table->foreignId('pengurus_id')->nullable()->after('id')->constrained('penguruses')->onDelete('cascade');
                }
                if (!Schema::hasColumn($tabelWali, 'status')) {
                    $table->enum('status', ['aktif', 'non_aktif'])->default('aktif')->after('pengurus_id');
                }
            });
        }
    }

    public function down()
    {
        // Hapus kolom jika rollback
        if (Schema::hasTable('muallim')) {
            Schema::table('muallim', function (Blueprint $table) {
                $table->dropForeign(['pengurus_id']);
                $table->dropColumn(['pengurus_id', 'status']);
            });
        }
        // ... (Ulangi untuk tabel lain jika perlu strict rollback)
    }
};
