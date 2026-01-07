<?php

namespace App\Http\Controllers;

use App\Models\Entitas;
use Illuminate\Http\Request;

class EntitasController extends Controller
{
    /**
     * Hanya untuk menampilkan form create entitas.
     * Index-nya ditampilkan oleh MasterJabatanController.
     */
    public function create()
    {
        return view('master.jabatan.entitas.create');
    }

    /**
     * Menyimpan entitas baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_entitas' => 'required'
        ]);

        Entitas::create([
            'nama_entitas' => $request->nama_entitas
        ]);

        return redirect()->route('master.jabatan.index')
            ->with('success', 'Entitas Pengurus berhasil ditambahkan.');
    }


    // public function edit($id)
    // {
    //     $entitas = Entitas::findOrFail($id);
    //     return view('master.jabatan.entitas.edit', compact('entitas'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nama_entitas' => 'required|string|max:255'
    //     ]);

    //     $entitas = Entitas::findOrFail($id);
    //     $entitas->update($request->all());

    //     return redirect()->route('master.jabatan.entitas.index')
    //         ->with('success', 'Entitas Pengurus berhasil diperbarui');
    // }

    // public function destroy($id)
    // {
    //     Entitas::destroy($id);
    //     return back()->with('success', 'Data berhasil dihapus');
    // }
}
