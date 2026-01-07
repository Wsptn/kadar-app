<?php

namespace App\Http\Controllers;

use App\Models\MasterTugasEksternal;
use App\Models\RangkapTugasEksternal;
use Illuminate\Http\Request;

class TugasEksternalController extends Controller
{
    public function index()
    {
        // Ambil semua data tugas dari tabel eksternal
        $TugasEksternal = MasterTugasEksternal::all();

        // Kirim data ke view
        return view('master.eksternal.index', compact('TugasEksternal'));
    }
    public function create()
    {
        return view('master.eksternal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'eksternal' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {

            MasterTugasEksternal::create([
                'eksternal' => $request->eksternal,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('master.eksternal.index')
                ->with('success', 'Tugas Internal berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }
    public function edit($id_eksternal)
    {
        $eksternal = MasterTugasEksternal::findOrFail($id_eksternal);

        return view('master.eksternal.edit', compact('eksternal'));
    }
    public function update(Request $request, $id_eksternal)
    {
        $request->validate([
            'eksternal' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $eksternal = MasterTugasEksternal::findOrFail($id_eksternal);

            $eksternal->update([
                'eksternal' => $request->eksternal,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()
                ->route('master.eksternal.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }
}
