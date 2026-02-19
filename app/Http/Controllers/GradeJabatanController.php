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

    public function edit($id)
    {
        $grade = GradeJabatan::findOrFail($id);
        $entitas = Entitas::orderBy('nama_entitas')->get();
        $jabatans = Jabatan::where('entitas_id', $grade->entitas_id)->get();
        $jenis = JenisJabatan::where('jabatan_id', $grade->jabatan_id)->get();

        return view('master.jabatan.grade.edit', compact('grade', 'entitas', 'jabatans', 'jenis'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'entitas_id'        => 'required|exists:entitas,id',
            'jabatan_id'        => 'required|exists:jabatans,id',
            'jenis_jabatan_id'  => 'required|exists:jenis_jabatans,id',
            'grade'             => 'required',
            'keterangan'        => 'nullable',
        ]);

        $grade = GradeJabatan::findOrFail($id);
        $grade->update($request->all());

        return redirect()->route('master.jabatan.index')
            ->with('success', 'Grade Jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $grade = GradeJabatan::findOrFail($id);
            $grade->delete();
            return back()->with('success', 'Grade Jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
