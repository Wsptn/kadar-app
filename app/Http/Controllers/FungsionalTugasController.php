<?php

namespace App\Http\Controllers;

use App\Models\FungsionalTugas;
use App\Models\MasterFungsionalTugas;
use Illuminate\Http\Request;

class FungsionalTugasController extends Controller
{
    public function index()
    {
        // Ambil semua data tugas dari tabel fungsional_tugas
        $FungsionalTugas = MasterFungsionalTugas::all();

        // Kirim data ke view
        return view('master.tugas.index', compact('FungsionalTugas'));
    }
    public function create()
    {
        return view('master.tugas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tugas' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {

            MasterFungsionalTugas::create([
                'tugas' => $request->tugas,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('master.tugas.index')
                ->with('success', 'Fungsional tugas berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }
    public function edit($id_tugas)
    {
        $tugas = MasterFungsionalTugas::findOrFail($id_tugas);

        return view('master.tugas.edit', compact('tugas'));
    }
    public function update(Request $request, $id_tugas)
    {
        $request->validate([
            'tugas' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $tugas = MasterFungsionalTugas::findOrFail($id_tugas);

            $tugas->update([
                'tugas' => $request->tugas,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()
                ->route('master.tugas.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }
}
