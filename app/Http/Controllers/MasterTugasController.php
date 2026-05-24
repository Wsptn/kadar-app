<?php

namespace App\Http\Controllers;

use App\Models\MasterTugas;
use Illuminate\Http\Request;

class MasterTugasController extends Controller
{
    public function index()
    {
        $tugasList = MasterTugas::orderBy('jenis_tugas')->orderBy('nama_tugas')->get();
        return view('master.tugas.index', compact('tugasList'));
    }

    public function create()
    {
        return view('master.tugas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'jenis_tugas' => 'required|in:fungsional,internal,eksternal',
        ]);

        MasterTugas::create($request->all());

        return redirect()->route('master.tugas.index')
            ->with('success', 'Data Master Tugas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tugas = MasterTugas::findOrFail($id);
        return view('master.tugas.edit', compact('tugas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'jenis_tugas' => 'required|in:fungsional,internal,eksternal',
        ]);

        $tugas = MasterTugas::findOrFail($id);
        $tugas->update($request->all());

        return redirect()->route('master.tugas.index')
            ->with('success', 'Data Master Tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tugas = MasterTugas::findOrFail($id);
        $tugas->delete();

        return redirect()->route('master.tugas.index')
            ->with('success', 'Data Master Tugas berhasil dihapus.');
    }
}
