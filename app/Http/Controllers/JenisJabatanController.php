<?php

namespace App\Http\Controllers;

use App\Models\Entitas;
use App\Models\Jabatan;
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



    public function edit($id)
    {
        $jenis = JenisJabatan::findOrFail($id);
        $entitas = Entitas::orderBy('nama_entitas')->get();
        $jabatans = Jabatan::where('entitas_id', $jenis->entitas_id)->get();

        return view('master.jabatan.jenis.edit', compact('jenis', 'entitas', 'jabatans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'entitas_id'    => 'required|exists:entitas,id',
            'jabatan_id'    => 'required|exists:jabatans,id',
            'jenis_jabatan' => 'required'
        ]);

        $jenis = JenisJabatan::findOrFail($id);
        $jenis->update([
            'entitas_id'    => $request->entitas_id,
            'jabatan_id'    => $request->jabatan_id,
            'jenis_jabatan' => $request->jenis_jabatan,
        ]);

        return redirect()->route('master.jabatan.index')
            ->with('success', 'Jenis Jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $jenis = JenisJabatan::findOrFail($id);

            if ($jenis->grade()->count() > 0) {
                return back()->with('error', 'Gagal hapus! Data ini masih digunakan di tab Grade Jabatan.');
            }

            $jenis->delete();
            return back()->with('success', 'Jenis Jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}
