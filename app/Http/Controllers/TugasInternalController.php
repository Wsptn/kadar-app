<?php

namespace App\Http\Controllers;

use App\Models\MasterTugasInternal;
use App\Models\RangkapTugasInternal;
use Illuminate\Http\Request;

class TugasInternalController extends Controller
{
    public function index()
    {
        // Ambil semua data tugas dari tabel Internal
        $TugasInternal = MasterTugasInternal::all();

        // Kirim data ke view
        return view('master.internal.index', compact('TugasInternal'));
    }
    public function create()
    {
        return view('master.internal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'internal' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {

            MasterTugasInternal::create([
                'internal' => $request->internal,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('master.internal.index')
                ->with('success', 'Tugas Internal berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }
    public function edit($id_internal)
    {
        $internal = MasterTugasInternal::findOrFail($id_internal);

        return view('master.internal.edit', compact('internal'));
    }
    public function update(Request $request, $id_internal)
    {
        $request->validate([
            'internal' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $internal = MasterTugasInternal::findOrFail($id_internal);

            $internal->update([
                'internal' => $request->internal,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()
                ->route('master.internal.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }
}
