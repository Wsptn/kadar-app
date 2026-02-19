<?php

namespace App\Http\Controllers;

use App\Models\Entitas;
use Illuminate\Http\Request;

class EntitasController extends Controller
{
    public function create()
    {
        return view('master.jabatan.entitas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_entitas' => 'required|string|max:255'
        ]);

        Entitas::create([
            'nama_entitas' => $request->nama_entitas
        ]);

        return redirect()->route('master.jabatan.index')
            ->with('success', 'Entitas Pengurus berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $entitas = Entitas::findOrFail($id);
        return view('master.jabatan.entitas.edit', compact('entitas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_entitas' => 'required|string|max:255'
        ]);

        $entitas = Entitas::findOrFail($id);
        $entitas->update([
            'nama_entitas' => $request->nama_entitas
        ]);

        return redirect()->route('master.jabatan.index')
            ->with('success', 'Entitas Pengurus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $entitas = Entitas::findOrFail($id);

        // Cek relasi: Jangan hapus jika sudah ada jabatan yang terhubung
        if ($entitas->jabatan()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Masih ada data Jabatan yang menggunakan entitas ini.');
        }

        $entitas->delete();
        return back()->with('success', 'Entitas berhasil dihapus.');
    }
}
