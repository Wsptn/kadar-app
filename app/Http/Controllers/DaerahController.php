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
    public function edit($id)
    {
        $daerah = Daerah::findOrFail($id);
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();

        return view('master.domisili.daerah.edit', compact('daerah', 'wilayah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayahs,id',
            'nama_daerah' => 'required|string|max:255'
        ]);

        $daerah = Daerah::findOrFail($id);
        $daerah->update([
            'wilayah_id' => $request->wilayah_id,
            'nama_daerah' => $request->nama_daerah
        ]);

        return redirect()->route('master.domisili.index')
            ->with('success', 'Daerah berhasil diperbarui');
    }

    public function destroy($id)
    {
        $daerah = Daerah::findOrFail($id);

        // Proteksi: Cek jika daerah masih memiliki kamar terkait
        if ($daerah->kamar()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Masih ada data Kamar yang terhubung dengan daerah ini.');
        }

        $daerah->delete();
        return back()->with('success', 'Daerah berhasil dihapus');
    }
}
