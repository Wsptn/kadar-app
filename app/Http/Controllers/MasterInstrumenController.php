<?php

namespace App\Http\Controllers;

use App\Models\MasterInstrumen;
use Illuminate\Http\Request;

class MasterInstrumenController extends Controller
{
    public function index()
    {
        $instrumens = MasterInstrumen::orderBy('aspek')->orderBy('id')->get();
        return view('master.instrumen.index', compact('instrumens'));
    }

    public function create()
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Biktren']), 403, 'Anda tidak memiliki akses ke halaman ini.');
        return view('master.instrumen.create');
    }

    public function store(Request $request)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Biktren']), 403, 'Anda tidak memiliki akses ke halaman ini.');

        $request->validate([
            'aspek' => 'required|string|max:255',
            'indikator' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'bobot' => 'required|numeric|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($request->status == 'aktif') {
            $totalBobot = MasterInstrumen::where('status', 'aktif')->sum('bobot');
            if (($totalBobot + $request->bobot) > 100) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Total bobot instrumen aktif melebihi 100%. (Total saat ini: ' . $totalBobot . '%)');
            }
        }

        MasterInstrumen::create($request->all());

        return redirect()->route('master.instrumen.index')->with('success', 'Instrumen berhasil ditambahkan.');
    }

    public function edit($id)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Biktren']), 403, 'Anda tidak memiliki akses ke halaman ini.');
        $instrumen = MasterInstrumen::findOrFail($id);
        return view('master.instrumen.edit', compact('instrumen'));
    }

    public function update(Request $request, $id)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Biktren']), 403, 'Anda tidak memiliki akses ke halaman ini.');
        $instrumen = MasterInstrumen::findOrFail($id);

        $request->validate([
            'aspek' => 'required|string|max:255',
            'indikator' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'bobot' => 'required|numeric|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($request->status == 'aktif') {
            // Calculate total excluding the current being updated
            $totalBobot = MasterInstrumen::where('status', 'aktif')->where('id', '!=', $id)->sum('bobot');
            if (($totalBobot + $request->bobot) > 100) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Total bobot instrumen aktif melebihi 100%. (Total instrumen aktif lainnya: ' . $totalBobot . '%)');
            }
        }

        $instrumen->update($request->all());

        return redirect()->route('master.instrumen.index')->with('success', 'Instrumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        abort_if(!in_array(auth()->user()->level, ['Admin', 'Biktren']), 403, 'Anda tidak memiliki akses ke halaman ini.');
        $instrumen = MasterInstrumen::findOrFail($id);
        
        // Prevent deletion if it has details
        if ($instrumen->kinerjaDetails()->count() > 0) {
            return redirect()->route('master.instrumen.index')->with('error', 'Gagal: Instrumen tidak dapat dihapus karena sudah memiliki riwayat penilaian. Ubah status menjadi Nonaktif sebagai gantinya.');
        }

        $instrumen->delete();

        return redirect()->route('master.instrumen.index')->with('success', 'Instrumen berhasil dihapus.');
    }
}
