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
        // Gunakan 'penguruses.status' agar aman dari error ambiguous column
        $totalPengurus = Pengurus::where('penguruses.status', 'aktif')->count();

        // Hitung Data Pendukung
        $totalMuallim  = Muallim::where('status', 'aktif')->count();
        $totalWaliAsuh = WaliAsuh::where('status', 'aktif')->count();
        $totalPengajar = Pengajar::where('status', 'aktif')->count();

        // ===============================
        // 2. GRAFIK PENGURUS PER WILAYAH (LOGIKA BARU)
        // ===============================
        // KITA UBAH: Start dari tabel 'wilayahs' (Master), bukan 'pengurus'.
        // Tujuannya agar SEMUA wilayah tetap terambil meski jumlah pengurusnya sedikit atau 0.

        $pengurusPerWilayah = DB::table('wilayahs')
            ->leftJoin('penguruses', function ($join) {
                $join->on('wilayahs.id', '=', 'penguruses.wilayah_id')
                    ->where('penguruses.status', '=', 'aktif'); // Hanya hitung yang aktif
            })
            ->select(
                'wilayahs.nama_wilayah',
                DB::raw('COUNT(penguruses.id) as total')
            )
            ->groupBy('wilayahs.nama_wilayah')
            ->orderBy('wilayahs.nama_wilayah', 'asc') // Urutkan nama wilayah A-Z agar rapi di grafik
            ->get();

        $labelsWilayah = $pengurusPerWilayah->pluck('nama_wilayah');
        $dataWilayah   = $pengurusPerWilayah->pluck('total');

        // ===============================
        // 3. GRAFIK RANGKAP TUGAS (Hanya Pengurus Aktif)
        // ===============================
        $rangkapInternal = Pengurus::where('penguruses.status', 'aktif')
            ->whereNotNull('rangkap_internal_id')->count();

        $tidakInternal   = $totalPengurus - $rangkapInternal;

        $rangkapEksternal = Pengurus::where('penguruses.status', 'aktif')
            ->whereNotNull('rangkap_eksternal_id')->count();

        $tidakEksternal   = $totalPengurus - $rangkapEksternal;

        // ===============================
        // 4. GRAFIK FUNGSIONAL TUGAS (METODE LEFT JOIN)
        // ===============================

        $fungsional = DB::table('master_fungsional_tugas')
            ->leftJoin('pengurus_fungsional_tugas', function ($join) {
                $join->on('pengurus_fungsional_tugas.master_fungsional_tugas_id', '=', 'master_fungsional_tugas.id_tugas')
                    ->where('pengurus_fungsional_tugas.status', '=', 'aktif');
            })
            ->leftJoin('penguruses', function ($join) {
                $join->on('penguruses.id', '=', 'pengurus_fungsional_tugas.pengurus_id')
                    ->where('penguruses.status', '=', 'aktif');
            })
            ->select(
                'master_fungsional_tugas.tugas',
                DB::raw('COUNT(penguruses.id) as total')
            )
            ->groupBy('master_fungsional_tugas.tugas')
            ->orderBy('master_fungsional_tugas.tugas')
            ->get();

        $labelFungsional = $fungsional->pluck('tugas');
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
