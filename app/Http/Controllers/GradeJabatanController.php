<?php

namespace App\Http\Controllers;

use App\Models\Entitas;
use App\Models\GradeJabatan;
use App\Models\Jabatan;
use App\Models\JenisJabatan;
use Illuminate\Http\Request;

class GradeJabatanController extends Controller
{
    public function create()
    {
        $entitas = Entitas::orderBy('nama_entitas')->get();
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        $jenis = JenisJabatan::orderBy('jenis_jabatan')->get();

        return view('master.jabatan.grade.create', compact('entitas', 'jabatan', 'jenis'));
    }

    /**
     * Menyimpan grade jabatan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'entitas_id'        => 'required|exists:entitas,id',
            'jabatan_id'        => 'required|exists:jabatans,id',
            'jenis_jabatan_id'  => 'required|exists:jenis_jabatans,id',
            'grade'             => 'required',
            'keterangan'        => 'nullable',
        ]);

        GradeJabatan::create([
            'entitas_id'        => $request->entitas_id,
            'jabatan_id'        => $request->jabatan_id,
            'jenis_jabatan_id'  => $request->jenis_jabatan_id,
            'grade'             => $request->grade,
            'keterangan'        => $request->keterangan,
        ]);

        return redirect()->route('master.jabatan.index')
            ->with('success', 'Grade Jabatan berhasil ditambahkan.');
    }

    // public function edit($id)
    // {
    //     $data = GradeJabatan::findOrFail($id);
    //     return view('master.jabatan.grade.edit', compact('data'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'grade' => 'required',
    //         'keterangan' => 'nullable'
    //     ]);

    //     GradeJabatan::findOrFail($id)->update($request->all());

    //     return redirect()->route('master.jabatan.grade.index')
    //         ->with('success', 'Grade Jabatan berhasil diperbarui.');
    // }

    // public function destroy($id)
    // {
    //     GradeJabatan::destroy($id);
    //     return back()->with('success', 'Grade Jabatan berhasil dihapus.');
    // }
}
