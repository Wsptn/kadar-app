<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entitas;
use App\Models\Jabatan;
use App\Models\JenisJabatan;
use App\Models\GradeJabatan;

class JabatanController extends Controller
{
    public function index()
    {
        // Ambil semua entitas, jabatan, jenis, dan grade untuk ditampilkan di tab
        $entitas = Entitas::orderBy('nama_entitas')->get();

        $jabatan = Jabatan::with('entitas')
            ->orderBy('id', 'DESC')
            ->get();

        // pastikan nama relasi di model JenisJabatan sesuai (mis. entitas, jabatan)
        $jenis = JenisJabatan::with(['entitas', 'jabatan'])
            ->orderBy('id', 'DESC')
            ->get();

        // pastikan nama relasi di model GradeJabatan sesuai (mis. entitas, jabatan, jenis)
        $grade = GradeJabatan::with(['entitas', 'jabatan', 'jenis'])
            ->orderBy('id', 'DESC')
            ->get();

        return view('master.jabatan.index', compact('entitas', 'jabatan', 'jenis', 'grade'));
    }
    /**
     * Tampilkan halaman master jabatan.
     */
    public function create()
    {
        $entitas = Entitas::orderBy('nama_entitas')->get();
        return view('master.jabatan.jabatan.create', compact('entitas'));
    }

    /**
     * Simpan data jabatan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'entitas_id'   => 'required|exists:entitas,id',
            'nama_jabatan' => 'required'
        ]);

        Jabatan::create([
            'entitas_id'   => $request->entitas_id,
            'nama_jabatan' => $request->nama_jabatan
        ]);

        return redirect()->route('master.jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function getByEntitas($entitas_id)
    {
        return Jabatan::where('entitas_id', $entitas_id)->get();
    }


    // public function edit($id)
    // {
    //     $data = Jabatan::findOrFail($id);
    //     $jenis = JenisJabatan::all();
    //     $grade = GradeJabatan::all();
    //     $entitas = Entitas::all();

    //     return view('master.jabatan.jabatan.edit', compact('data', 'jenis', 'grade', 'entitas'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nama_jabatan' => 'required',
    //         'jenis_jabatan_id' => 'required',
    //         'grade_id' => 'required',
    //         'entitas_id' => 'required',
    //     ]);

    //     Jabatan::findOrFail($id)->update($request->all());

    //     return redirect()->route('master.jabatan.jabatan.index')
    //         ->with('success', 'Jabatan berhasil diperbarui.');
    // }

    // public function destroy($id)
    // {
    //     Jabatan::destroy($id);
    //     return back()->with('success', 'Jabatan berhasil dihapus.');
    // }
}
