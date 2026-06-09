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
        $totalPengurus = Pengurus::where('pengurus.status', 'aktif')->count();

        // Hitung Data Pendukung berdasarkan Fungsional Tugas Pengurus
        $totalMuallim = Pengurus::where('status', 'aktif')
            ->whereHas('fungsionalTugas', function ($q) {
                $q->where('nama_tugas', "Mu'allim")
                  ->where('tugas_detail.status', 'aktif');
            })->count();

        $totalWaliAsuh = Pengurus::where('status', 'aktif')
            ->whereHas('fungsionalTugas', function ($q) {
                $q->where('nama_tugas', "Wali Asuh")
                  ->where('tugas_detail.status', 'aktif');
            })->count();

        $totalPengajar = Pengurus::where('status', 'aktif')
            ->whereHas('fungsionalTugas', function ($q) {
                $q->where('nama_tugas', "Pengajar")
                  ->where('tugas_detail.status', 'aktif');
            })->count();

        // ===============================
        // 2. GRAFIK PENGURUS PER WILAYAH (LOGIKA BARU)
        // ===============================
        // KITA UBAH: Start dari tabel 'wilayahs' (Master).
        // Hubungkan ke daerahs, kamars, dan penguruses.

        $pengurusPerWilayah = DB::table('wilayah')
            ->leftJoin('daerah', 'wilayah.id', '=', 'daerah.wilayah_id')
            ->leftJoin('kamar', 'daerah.id', '=', 'kamar.daerah_id')
            ->leftJoin('pengurus', function ($join) {
                $join->on('kamar.id', '=', 'pengurus.kamar_id')
                    ->where('pengurus.status', '=', 'aktif'); // Hanya hitung yang aktif
            })
            ->select(
                'wilayah.nama_wilayah as nama_wilayah',
                DB::raw('COUNT(pengurus.id) as total')
            )
            ->groupBy('wilayah.nama_wilayah')
            ->orderBy('wilayah.nama_wilayah', 'asc') // Urutkan nama wilayah A-Z agar rapi di grafik
            ->get();

        $labelsWilayah = $pengurusPerWilayah->pluck('nama_wilayah');
        $dataWilayah   = $pengurusPerWilayah->pluck('total');

        // ===============================
        // 3. GRAFIK RANGKAP TUGAS (Hanya Pengurus Aktif)
        // ===============================
        $rangkapInternal = Pengurus::where('pengurus.status', 'aktif')
            ->whereHas('internalTugas', function ($q) {
                $q->where('tugas_detail.status', 'aktif');
            })->count();

        $tidakInternal   = $totalPengurus - $rangkapInternal;

        $rangkapEksternal = Pengurus::where('pengurus.status', 'aktif')
            ->whereHas('eksternalTugas', function ($q) {
                $q->where('tugas_detail.status', 'aktif');
            })->count();

        $tidakEksternal   = $totalPengurus - $rangkapEksternal;

        // ===============================
        // 4. GRAFIK FUNGSIONAL TUGAS (METODE LEFT JOIN)
        // ===============================

        $fungsional = DB::table('tugas')
            ->where('jenis_tugas', 'fungsional')
            ->leftJoin('tugas_detail', function ($join) {
                $join->on('tugas_detail.tugas_id', '=', 'tugas.id')
                    ->where('tugas_detail.status', '=', 'aktif');
            })
            ->leftJoin('pengurus', function ($join) {
                $join->on('pengurus.id', '=', 'tugas_detail.pengurus_id')
                    ->where('pengurus.status', '=', 'aktif');
            })
            ->select(
                'tugas.nama_tugas',
                DB::raw('COUNT(pengurus.id) as total')
            )
            ->groupBy('tugas.nama_tugas')
            ->orderBy('tugas.nama_tugas')
            ->get();

        $labelFungsional = $fungsional->pluck('nama_tugas');
        $dataFungsional  = $fungsional->pluck('total');

        // ===============================
        // 5. STATUS KEAKTIFAN PENGURUS
        // ===============================
        $jumlahAktif    = Pengurus::where('pengurus.status', 'aktif')->count();
        $jumlahNonAktif = Pengurus::where('pengurus.status', 'non_aktif')->count();

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
