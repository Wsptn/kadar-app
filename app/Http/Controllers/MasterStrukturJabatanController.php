<?php

namespace App\Http\Controllers;

use App\Models\MasterStrukturJabatan;
use Illuminate\Http\Request;

class MasterStrukturJabatanController extends Controller
{
    public function index()
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah', 'Biktren']), 403, 'Unauthorized action.');
        $jabatans = MasterStrukturJabatan::orderBy('entitas')
            ->orderBy('jabatan')
            ->orderBy('jenis_jabatan')
            ->orderBy('grade')
            ->get();
        return view('master.struktur_jabatan.index', compact('jabatans'));
    }

    public function create()
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah', 'Biktren']), 403, 'Unauthorized action.');
        return view('master.struktur_jabatan.create');
    }

    public function store(Request $request)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah', 'Biktren']), 403, 'Unauthorized action.');
        $request->validate([
            'entitas' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'jenis_jabatan' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
        ]);

        MasterStrukturJabatan::create($request->all());

        return redirect()->route('master.struktur_jabatan.index')
            ->with('success', 'Data Struktur Jabatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah', 'Biktren']), 403, 'Unauthorized action.');
        $jabatan = MasterStrukturJabatan::findOrFail($id);
        return view('master.struktur_jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah', 'Biktren']), 403, 'Unauthorized action.');
        $request->validate([
            'entitas' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'jenis_jabatan' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
        ]);

        $jabatan = MasterStrukturJabatan::findOrFail($id);
        $jabatan->update($request->all());

        return redirect()->route('master.struktur_jabatan.index')
            ->with('success', 'Data Struktur Jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Wilayah', 'Biktren']), 403, 'Unauthorized action.');
        $jabatan = MasterStrukturJabatan::findOrFail($id);
        $jabatan->delete();

        return redirect()->route('master.struktur_jabatan.index')
            ->with('success', 'Data Struktur Jabatan berhasil dihapus.');
    }
}
