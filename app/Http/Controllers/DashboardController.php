<?php

namespace App\Http\Controllers;

use App\Models\MasterFungsionalTugas;
use App\Models\Pengurus;
use App\Models\Muallim;
use App\Models\WaliAsuh;
use App\Models\Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===============================
        // 1. STATISTIK UTAMA (HANYA YANG AKTIF)
        // ===============================
        // Karena kita sudah menambahkan kolom 'status' di tabel muallim/wali_asuh/pengajar
        // lewat migrasi sebelumnya, sekarang aman untuk menggunakan where('status', 'aktif').

        $totalPengurus = Pengurus::where('status', 'aktif')->count(); // Hanya pengurus aktif

        $totalMuallim  = Muallim::where('status', 'aktif')->count();
        $totalWaliAsuh = WaliAsuh::where('status', 'aktif')->count();
        $totalPengajar = Pengajar::where('status', 'aktif')->count();

        // ===============================
        // 2. GRAFIK PENGURUS PER WILAYAH
        // ===============================
        $pengurusPerWilayah = Pengurus::select(
            'wilayahs.nama_wilayah',
            DB::raw('COUNT(penguruses.id) as total')
        )
            ->join('wilayahs', 'wilayahs.id', '=', 'penguruses.wilayah_id')
            ->where('penguruses.status', 'aktif') // Filter hanya pengurus aktif
            ->groupBy('wilayahs.nama_wilayah')
            ->orderBy('wilayahs.nama_wilayah')
            ->get();

        $labelsWilayah = $pengurusPerWilayah->pluck('nama_wilayah');
        $dataWilayah   = $pengurusPerWilayah->pluck('total');

        // ===============================
        // 3. GRAFIK RANGKAP TUGAS (Hanya Pengurus Aktif)
        // ===============================
        $rangkapInternal = Pengurus::where('status', 'aktif')
            ->whereNotNull('rangkap_internal_id')->count();

        $tidakInternal   = $totalPengurus - $rangkapInternal;

        $rangkapEksternal = Pengurus::where('status', 'aktif')
            ->whereNotNull('rangkap_eksternal_id')->count();

        $tidakEksternal   = $totalPengurus - $rangkapEksternal;

        // ===============================
        // 4. GRAFIK FUNGSIONAL TUGAS (SANGAT PENTING)
        // ===============================
        // Logika: Ambil semua Master Tugas, lalu hitung jumlah pivot yang statusnya 'aktif'.
        // Menggunakan LEFT JOIN dengan kondisi filter DI DALAM JOIN agar kategori tugas tetap muncul meski jumlahnya 0.

        $fungsional = DB::table('master_fungsional_tugas')
            ->leftJoin('pengurus_fungsional_tugas', function ($join) {
                $join->on('pengurus_fungsional_tugas.master_fungsional_tugas_id', '=', 'master_fungsional_tugas.id_tugas')
                    ->where('pengurus_fungsional_tugas.status', '=', 'aktif'); // KUNCI: Hanya hitung yang aktif
            })
            ->select(
                'master_fungsional_tugas.tugas',
                DB::raw('COUNT(pengurus_fungsional_tugas.id) as total') // Hitung data yang match
            )
            ->groupBy('master_fungsional_tugas.tugas')
            ->orderBy('master_fungsional_tugas.tugas')
            ->get();

        $labelFungsional = $fungsional->pluck('tugas');
        $dataFungsional  = $fungsional->pluck('total');

        // ===============================
        // 5. STATUS KEAKTIFAN PENGURUS
        // ===============================
        // Ini menghitung global (termasuk yang non-aktif untuk perbandingan)
        $jumlahAktif    = Pengurus::where('status', 'aktif')->count();
        $jumlahNonAktif = Pengurus::where('status', 'non_aktif')->count();

        // ===============================
        // RETURN VIEW
        // ===============================
        return view('dashboard.index', [
            'totalPengurus'    => $totalPengurus,
            'totalWaliAsuh'    => $totalWaliAsuh,
            'totalPengajar'    => $totalPengajar,
            'totalMuallim'     => $totalMuallim,

            'jumlahAktif'      => $jumlahAktif,
            'jumlahNonAktif'   => $jumlahNonAktif,

            'labelsWilayah'    => $labelsWilayah,
            'dataWilayah'      => $dataWilayah,

            'rangkapInternal'  => $rangkapInternal,
            'tidakInternal'    => $tidakInternal,
            'rangkapEksternal' => $rangkapEksternal,
            'tidakEksternal'   => $tidakEksternal,

            'labelFungsional'  => $labelFungsional,
            'dataFungsional'   => $dataFungsional,
        ]);
    }
}
