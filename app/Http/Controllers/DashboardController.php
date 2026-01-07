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
        // FUNGSI STATISTIK ATAS
        // ===============================
        $muallimId  = MasterFungsionalTugas::where('tugas', "Mu'allim")->value('id_tugas');
        $waliAsuhId = MasterFungsionalTugas::where('tugas', "Wali Asuh")->value('id_tugas');
        $pengajarId = MasterFungsionalTugas::where('tugas', "Pengajar")->value('id_tugas');

        $totalMuallim  = Pengurus::where('fungsional_tugas_id', $muallimId)->count();
        $totalWaliAsuh = Pengurus::where('fungsional_tugas_id', $waliAsuhId)->count();
        $totalPengajar = Pengurus::where('fungsional_tugas_id', $pengajarId)->count();

        // ===============================
        // GRAFIK PENGURUS PER WILAYAH
        // ===============================
        $pengurusPerWilayah = Pengurus::select(
            'wilayahs.nama_wilayah',
            DB::raw('COUNT(penguruses.id) as total')
        )
            ->join('wilayahs', 'wilayahs.id', '=', 'penguruses.wilayah_id')
            ->groupBy('wilayahs.nama_wilayah')
            ->orderBy('wilayahs.nama_wilayah')
            ->get();


        $labelsWilayah = $pengurusPerWilayah->pluck('nama_wilayah');
        $dataWilayah   = $pengurusPerWilayah->pluck('total');

        // ===============================
        // GRAFIK RANGKAP TUGAS
        // ===============================
        $rangkapInternal = Pengurus::whereNotNull('rangkap_internal_id')->count();
        $tidakInternal   = Pengurus::whereNull('rangkap_internal_id')->count();

        $rangkapEksternal = Pengurus::whereNotNull('rangkap_eksternal_id')->count();
        $tidakEksternal   = Pengurus::whereNull('rangkap_eksternal_id')->count();

        // ===============================
        // GRAFIK FUNGSIONAL TUGAS
        // ===============================
        $fungsional = DB::table('master_fungsional_tugas')
            ->leftJoin('penguruses', 'penguruses.fungsional_tugas_id', '=', 'master_fungsional_tugas.id_tugas')
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
        // RETURN VIEW
        // ===============================
        return view('dashboard.index', [
            'totalPengurus'  => Pengurus::count(),
            'totalWaliAsuh'  => $totalWaliAsuh,
            'totalPengajar'  => $totalPengajar,
            'totalMuallim'   => $totalMuallim,

            // grafik status
            'jumlahAktif'    => Pengurus::where('status', 'aktif')->count(),
            'jumlahNonAktif' => Pengurus::where('status', 'non_aktif')->count(),

            // grafik wilayah
            'labelsWilayah'  => $labelsWilayah,
            'dataWilayah'    => $dataWilayah,

            // grafik rangkap
            'rangkapInternal'  => $rangkapInternal,
            'tidakInternal'    => $tidakInternal,
            'rangkapEksternal' => $rangkapEksternal,
            'tidakEksternal'   => $tidakEksternal,

            // grafik fungsional
            'labelFungsional' => $labelFungsional,
            'dataFungsional'  => $dataFungsional,
        ]);
    }
}
