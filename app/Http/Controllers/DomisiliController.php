<?php

namespace App\Http\Controllers;

use App\Models\Daerah;
use App\Models\Kamar;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class DomisiliController extends Controller
{
    public function index()
    {
        // Ambil semua data
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();
        $daerah  = Daerah::with('wilayah')->orderBy('nama_daerah')->get();
        $kamar   = Kamar::with(['wilayah', 'daerah'])->orderBy('nomor_kamar')->get();

        // Kirim ke view utama domisili
        return view('master.domisili.index', compact('wilayah', 'daerah', 'kamar'));
    }
}
