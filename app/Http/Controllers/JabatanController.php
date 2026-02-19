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
        $entitas = Entitas::orderBy('nama_entitas')->get();

        $jabatan = Jabatan::with('entitas')
            ->orderBy('id', 'DESC')
            ->get();

        $jenis = JenisJabatan::with(['entitas', 'jabatan'])
            ->orderBy('id', 'DESC')
            ->get();

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


    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);

        $entitas = Entitas::orderBy('nama_entitas')->get();

        return view('master.jabatan.jabatan.edit', compact('jabatan', 'entitas'));
    }

    /**
     * Perbarui data jabatan di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'entitas_id'   => 'required|exists:entitas,id',
            'nama_jabatan' => 'required|string|max:255'
        ]);

        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update([
            'entitas_id'   => $request->entitas_id,
            'nama_jabatan' => $request->nama_jabatan
        ]);

        // Redirect kembali ke index utama master jabatan
        return redirect()->route('master.jabatan.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    /**
     * Hapus data jabatan.
     */
    public function destroy($id)
    {
        try {
            $jabatan = Jabatan::findOrFail($id);

            $adaJenis = \App\Models\JenisJabatan::where('jabatan_id', $id)->exists();

            if ($adaJenis) {
                return back()->with('error', 'Gagal! Jabatan ini tidak bisa dihapus karena masih digunakan di tab "Jenis Jabatan". Hapus dulu jenis jabatannya.');
            }

            $adaPengurus = \App\Models\Pengurus::where('jabatan_id', $id)->exists();
            if ($adaPengurus) {
                return back()->with('error', 'Gagal! Jabatan ini sedang dipakai oleh data Pengurus. Ubah jabatan pengurus tersebut sebelum menghapus ini.');
            }

            $jabatan->delete();
            return back()->with('success', 'Jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Kesalahan sistem: ' . $e->getMessage());
        }
    }
}
