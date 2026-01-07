<?php

namespace App\Http\Controllers;

use App\Models\Daerah;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class DaerahController extends Controller
{
    public function create()
    {
        // Load wilayah untuk dropdown
        $wilayah = Wilayah::all();

        return view('master.domisili.daerah.create', compact('wilayah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayahs,id',
            'nama_daerah' => 'required|string|max:255'
        ]);

        Daerah::create([
            'wilayah_id' => $request->wilayah_id,
            'nama_daerah' => $request->nama_daerah
        ]);

        return redirect()->route('master.domisili.index')
            ->with('success', 'Daerah berhasil ditambahkan');
    }
}
