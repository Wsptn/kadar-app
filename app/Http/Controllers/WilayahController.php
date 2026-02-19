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
    public function edit($id)
    {
        $wilayah = Wilayah::findOrFail($id);
        return view('master.domisili.wilayah.edit', compact('wilayah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_wilayah' => 'required|string|max:255'
        ]);

        $wilayah = Wilayah::findOrFail($id);
        $wilayah->update([
            'nama_wilayah' => $request->nama_wilayah
        ]);

        return redirect()->route('master.domisili.index')
            ->with('success', 'Wilayah berhasil diperbarui');
    }

    public function destroy($id)
    {
        $wilayah = Wilayah::findOrFail($id);

        // Proteksi: Cek jika wilayah masih memiliki daerah terkait
        if ($wilayah->daerah()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Masih ada data Daerah yang terhubung dengan wilayah ini.');
        }

        $wilayah->delete();
        return back()->with('success', 'Wilayah berhasil dihapus');
    }
}
