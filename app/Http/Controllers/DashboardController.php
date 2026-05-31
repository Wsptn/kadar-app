<?php

namespace App\Http\Controllers;

use App\Models\MasterFungsionalTugas;
use App\Models\Pengurus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===============================
        // 1. STATISTIK UTAMA (HANYA YANG AKTIF)
        // ===============================
        // Gunakan 'penguruses.status' agar aman dari error ambiguous column
        $totalPengurus = Pengurus::where('penguruses.status', 'aktif')->count();

        // Hitung Data Pendukung berdasarkan Fungsional Tugas Pengurus
        $totalMuallim = Pengurus::where('status', 'aktif')
            ->whereHas('fungsionalTugas', function ($q) {
                $q->where('nama_tugas', "Mu'allim")
                  ->where('detail_tugas.status', 'aktif');
            })->count();

        $totalWaliAsuh = Pengurus::where('status', 'aktif')
            ->whereHas('fungsionalTugas', function ($q) {
                $q->where('nama_tugas', "Wali Asuh")
                  ->where('detail_tugas.status', 'aktif');
            })->count();

        $totalPengajar = Pengurus::where('status', 'aktif')
            ->whereHas('fungsionalTugas', function ($q) {
                $q->where('nama_tugas', "Pengajar")
                  ->where('detail_tugas.status', 'aktif');
            })->count();

        // ===============================
        // 2. GRAFIK PENGURUS PER WILAYAH (LOGIKA BARU)
        // ===============================
        // KITA UBAH: Start dari tabel 'wilayahs' (Master), bukan 'pengurus'.
        // Tujuannya agar SEMUA wilayah tetap terambil meski jumlah pengurusnya sedikit atau 0.

        $pengurusPerWilayah = DB::table('domisilis')
            ->leftJoin('penguruses', function ($join) {
                $join->on('domisilis.id', '=', 'penguruses.domisili_id')
                    ->where('penguruses.status', '=', 'aktif'); // Hanya hitung yang aktif
            })
            ->select(
                'domisilis.wilayah as nama_wilayah',
                DB::raw('COUNT(penguruses.id) as total')
            )
            ->groupBy('domisilis.wilayah')
            ->orderBy('domisilis.wilayah', 'asc') // Urutkan nama wilayah A-Z agar rapi di grafik
            ->get();

        $labelsWilayah = $pengurusPerWilayah->pluck('nama_wilayah');
        $dataWilayah   = $pengurusPerWilayah->pluck('total');

        // ===============================
        // 3. GRAFIK RANGKAP TUGAS (Hanya Pengurus Aktif)
        // ===============================
        $rangkapInternal = Pengurus::where('penguruses.status', 'aktif')
            ->whereHas('internalTugas', function ($q) {
                $q->where('detail_tugas.status', 'aktif');
            })->count();

        $tidakInternal   = $totalPengurus - $rangkapInternal;

        $rangkapEksternal = Pengurus::where('penguruses.status', 'aktif')
            ->whereHas('eksternalTugas', function ($q) {
                $q->where('detail_tugas.status', 'aktif');
            })->count();

        $tidakEksternal   = $totalPengurus - $rangkapEksternal;

        // ===============================
        // 4. GRAFIK FUNGSIONAL TUGAS (METODE LEFT JOIN)
        // ===============================

        $fungsional = DB::table('master_tugas')
            ->where('jenis_tugas', 'fungsional')
            ->leftJoin('detail_tugas', function ($join) {
                $join->on('detail_tugas.master_tugas_id', '=', 'master_tugas.id_tugas')
                    ->where('detail_tugas.status', '=', 'aktif');
            })
            ->leftJoin('penguruses', function ($join) {
                $join->on('penguruses.id', '=', 'detail_tugas.pengurus_id')
                    ->where('penguruses.status', '=', 'aktif');
            })
            ->select(
                'master_tugas.nama_tugas',
                DB::raw('COUNT(penguruses.id) as total')
            )
            ->groupBy('master_tugas.nama_tugas')
            ->orderBy('master_tugas.nama_tugas')
            ->get();

        $labelFungsional = $fungsional->pluck('nama_tugas');
        $dataFungsional  = $fungsional->pluck('total');

        // ===============================
        // 5. STATUS KEAKTIFAN PENGURUS
        // ===============================
        $jumlahAktif    = Pengurus::where('penguruses.status', 'aktif')->count();
        $jumlahNonAktif = Pengurus::where('penguruses.status', 'non_aktif')->count();

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
