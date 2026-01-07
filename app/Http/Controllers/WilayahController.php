<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function create()
    {
        return view('master.domisili.wilayah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_wilayah' => 'required|string|max:255'
        ]);

        Wilayah::create([
            'nama_wilayah' => $request->nama_wilayah
        ]);

        return redirect()->route('master.domisili.index')
            ->with('success', 'Wilayah berhasil ditambahkan');
    }
}
