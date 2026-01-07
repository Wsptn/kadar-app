<?php

namespace App\Http\Controllers;

use App\Models\Entitas;
use App\Models\JenisJabatan;
use Illuminate\Http\Request;

class JenisJabatanController extends Controller
{
    public function create()
    {
        $entitas = Entitas::orderBy('nama_entitas')->get();
        return view('master.jabatan.jenis.create', compact('entitas'));
    }

    /**
     * Simpan data jenis jabatan
     */
    public function store(Request $request)
    {
        $request->validate([
            'entitas_id'           => 'required|exists:entitas,id',
            'jabatan_id'           => 'required|exists:jabatans,id',
            'jenis_jabatan'        => 'required'
        ]);

        JenisJabatan::create([
            'entitas_id'           => $request->entitas_id,
            'jabatan_id'           => $request->jabatan_id,
            'jenis_jabatan'        => $request->jenis_jabatan
        ]);

        return redirect()->route('master.jabatan.index')
            ->with('success', 'Jenis Jabatan berhasil ditambahkan.');
    }

    public function getByJabatan($jabatan_id)
    {
        return JenisJabatan::where('jabatan_id', $jabatan_id)->get();
    }
    public function getJenis($jabatan_id)
    {
        $jenis = JenisJabatan::where('jabatan_id', $jabatan_id)->get();

        return response()->json($jenis);
    }



    // public function edit($id)
    // {
    //     $data = JenisJabatan::findOrFail($id);
    //     return view('master.jabatan.jenis.edit', compact('data'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nama_jenis_jabatan' => 'required'
    //     ]);

    //     JenisJabatan::findOrFail($id)->update([
    //         'nama_jenis_jabatan' => $request->nama_jenis_jabatan,
    //     ]);

    //     return redirect()->route('master.jabatan.jenis.index')
    //         ->with('success', 'Jenis Jabatan berhasil diperbarui.');
    // }

    // public function destroy($id)
    // {
    //     JenisJabatan::destroy($id);
    //     return back()->with('success', 'Jenis Jabatan berhasil dihapus.');
    // }
}
