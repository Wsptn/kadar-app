<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_instrumens', function (Blueprint $table) {
            $table->id();
            $table->string('aspek');
            $table->string('indikator');
            $table->text('keterangan')->nullable();
            $table->integer('bobot');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // Insert initial data
        DB::table('master_instrumens')->insert([
            ['aspek' => 'Kedisiplinan dan Kehadiran', 'indikator' => 'Kedisiplinan Waktu', 'keterangan' => 'Tepat waktu dalam mengikuti dan melaksanakan tugas sesuai dengan kalender kegiatan.', 'bobot' => 13, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aspek' => 'Kedisiplinan dan Kehadiran', 'indikator' => 'Kehadiran (Tanggung Jawab)', 'keterangan' => 'Tidak meninggalkan tanggung jawab tanpa izin.', 'bobot' => 11, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aspek' => 'Tanggung Jawab dan Loyalitas', 'indikator' => 'Penyelesaian Tugas', 'keterangan' => 'Menyelesaikan tugas sesuai waktu yang telah direncanakan.', 'bobot' => 12, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aspek' => 'Tanggung Jawab dan Loyalitas', 'indikator' => 'Loyalitas', 'keterangan' => 'Menunjukkan loyalitas terhadap kebutuhan daerah, wilayah dan pesantren.', 'bobot' => 8, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aspek' => 'Akhlak dan Keteladanan', 'indikator' => 'Akhlak', 'keterangan' => 'Berperilaku sopan, berakhlak baik dan mengikuti semua bentuk aturan pesantren.', 'bobot' => 14, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aspek' => 'Akhlak dan Keteladanan', 'indikator' => 'Keteladanan', 'keterangan' => 'Menjadi contoh bagi santri dan sesama pengurus.', 'bobot' => 12, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aspek' => 'Kinerja dan Inisiatif', 'indikator' => 'Tupoksi', 'keterangan' => 'Bekerja sesuai tupoksi yang telah di tentukan oleh Kepala Biro Kepesantrenan.', 'bobot' => 11, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aspek' => 'Kinerja dan Inisiatif', 'indikator' => 'Komunikasi', 'keterangan' => 'Mampu mengkomunikasikan ide secara lisan maupun tertulis sehingga dipahami pengurus lain.', 'bobot' => 7, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aspek' => 'Kepemimpinan dan Kerja Sama', 'indikator' => 'Koordinasi', 'keterangan' => 'Mampu mengatur diri dan berkoordinasi secara utuh dengan divisi lain dan satuan kerja terkait.', 'bobot' => 7, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aspek' => 'Kepemimpinan dan Kerja Sama', 'indikator' => 'Kebersamaan', 'keterangan' => 'Membangun kebersamaan sesama pengurus Wilayah/Daerah.', 'bobot' => 5, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('master_instrumens');
    }
};
