<?php

namespace App\Http\Controllers;

use App\Models\Pendidikan;
use Illuminate\Http\Request;

class PendidikanController extends Controller
{
    public function index()
    {
        $pendidikan = Pendidikan::orderBy('nama_pendidikan')->get();

        return view('master.pendidikan.index', compact('pendidikan'));
    }

    /**
     * Form tambah pendidikan
     */
    public function create()
    {
        return view('master.pendidikan.create');
    }

    /**
     * Simpan data pendidikan ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pendidikan' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        try {
            Pendidikan::create([
                'nama_pendidikan' => $request->nama_pendidikan,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('master.pendidikan.index')
                ->with('success', 'Data pendidikan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Form edit pendidikan
     */
    public function edit($id_pendidikan)
    {
        $pendidikan = Pendidikan::findOrFail($id_pendidikan);

        return view('master.pendidikan.edit', compact('pendidikan'));
    }

    /**
     * Update data pendidikan
     */
    public function update(Request $request, $id_pendidikan)
    {
        $request->validate([
            'nama_pendidikan' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $pendidikan = Pendidikan::findOrFail($id_pendidikan);

            $pendidikan->update([
                'nama_pendidikan' => $request->nama_pendidikan,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('master.pendidikan.index')
                ->with('success', 'Data pendidikan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }
}
