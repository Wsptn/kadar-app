<?php

namespace App\Http\Controllers;

use App\Models\Daerah;
use App\Models\Kamar;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function create()
    {
        $wilayah = Wilayah::all();
        $daerah  = Daerah::all();

        return view('master.domisili.kamar.create', compact('wilayah', 'daerah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayahs,id',
            'daerah_id' => 'required|exists:daerahs,id',
            'nomor_kamar' => 'required|string|max:50'
        ]);

        Kamar::create([
            'wilayah_id'  => $request->wilayah_id,
            'daerah_id'   => $request->daerah_id,
            'nomor_kamar' => $request->nomor_kamar,
        ]);

        return redirect()->route('master.domisili.index')
            ->with('success', 'Kamar berhasil ditambahkan');
    }
}
