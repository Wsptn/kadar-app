<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use Illuminate\Http\Request;

class AngkatanController extends Controller
{
    public function index()
    {
        // Ambil semua data angkatan
        $angkatan = Angkatan::all();

        // Kirim ke view
        return view('master.angkatan.index', compact('angkatan'));
    }
    public function create()
    {
        return view('master.angkatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'angkatan' => 'required|string|max:25',
            'keterangan' => 'nullable|string',
        ]);

        try {

            Angkatan::create([
                'angkatan' => $request->angkatan,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('master.angkatan.index')
                ->with('success', 'Tugas Internal berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }
    public function edit($id_angkatan)
    {
        $angkatan = Angkatan::findOrFail($id_angkatan);

        return view('master.angkatan.edit', compact('angkatan'));
    }
    public function update(Request $request, $id_angkatan)
    {
        $request->validate([
            'angkatan' => 'required|string|max:25',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $angkatan = Angkatan::findOrFail($id_angkatan);

            $angkatan->update([
                'angkatan' => $request->angkatan,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()
                ->route('master.angkatan.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }
}
