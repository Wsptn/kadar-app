<?php

namespace App\Http\Controllers;

use App\Models\EntitasDaerah;
use Illuminate\Http\Request;

class EntitasDaerahController extends Controller
{
    public function index()
    {
        $entitasDaerah = EntitasDaerah::orderBy('nama_entitas')->get();
        return view('master.entitas_daerah.index', compact('entitasDaerah'));
    }

    public function create()
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');
        return view('master.entitas_daerah.create');
    }

    public function store(Request $request)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $request->validate([
            'nama_entitas' => 'required|string|max:255|unique:entitas_daerahs,nama_entitas',
        ]);

        EntitasDaerah::create([
            'nama_entitas' => $request->nama_entitas,
        ]);

        return redirect()->route('master.entitas_daerah.index')->with('success', 'Entitas Daerah berhasil ditambahkan.');
    }

    public function edit($id)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');
        $entitasDaerah = EntitasDaerah::findOrFail($id);
        return view('master.entitas_daerah.edit', compact('entitasDaerah'));
    }

    public function update(Request $request, $id)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $entitasDaerah = EntitasDaerah::findOrFail($id);
        
        $request->validate([
            'nama_entitas' => 'required|string|max:255|unique:entitas_daerahs,nama_entitas,' . $id,
        ]);

        $entitasDaerah->update([
            'nama_entitas' => $request->nama_entitas,
        ]);

        return redirect()->route('master.entitas_daerah.index')->with('success', 'Entitas Daerah berhasil diperbarui.');
    }

    public function destroy($id)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $entitasDaerah = EntitasDaerah::findOrFail($id);
        $entitasDaerah->delete();

        return redirect()->route('master.entitas_daerah.index')->with('success', 'Entitas Daerah berhasil dihapus.');
    }
}
