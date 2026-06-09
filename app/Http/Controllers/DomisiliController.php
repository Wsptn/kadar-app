<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\Daerah;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DomisiliController extends Controller
{
    public function index()
    {
        $userLevel = Auth::user()->level;
        $hasAccess = $userLevel == 'Admin' || $userLevel == 'Wilayah';

        $kamars = Kamar::with('daerah.wilayah')->get();

        $domisilis = $kamars->map(function($k) {
            return (object) [
                'id' => $k->id,
                'wilayah' => $k->daerah->wilayah->nama_wilayah ?? '-',
                'daerah' => $k->daerah->nama_daerah ?? '-',
                'entitas_daerah' => $k->daerah->entitas_daerah ?? '-',
                'kamar' => $k->nomor_kamar
            ];
        })->sortBy(['wilayah', 'daerah', 'kamar'])->values();

        return view('master.domisili.index', compact('domisilis', 'hasAccess'));
    }

    public function create()
    {
        $userLevel = Auth::user()->level;
        abort_if(!in_array($userLevel, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        return view('master.domisili.create');
    }

    public function store(Request $request)
    {
        $userLevel = Auth::user()->level;
        abort_if(!in_array($userLevel, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $request->validate([
            'wilayah' => 'required|string|max:255',
            'daerah'  => 'required|string|max:255',
            'entitas_daerah' => 'nullable|string|max:255',
            'kamar'   => 'required|string|max:255',
        ]);

        $wilayah = Wilayah::firstOrCreate(['nama_wilayah' => $request->wilayah]);
        $daerah = Daerah::where('nama_daerah', $request->daerah)
            ->where('wilayah_id', $wilayah->id)
            ->first();

        if (!$daerah) {
            $daerah = Daerah::create([
                'nama_daerah' => $request->daerah,
                'wilayah_id' => $wilayah->id,
                'entitas_daerah' => $request->entitas_daerah
            ]);
        } elseif ($request->filled('entitas_daerah') && $daerah->entitas_daerah !== $request->entitas_daerah) {
            $daerah->update(['entitas_daerah' => $request->entitas_daerah]);
        }

        // Cek duplikasi
        $exists = Kamar::where('nomor_kamar', $request->kamar)
            ->where('daerah_id', $daerah->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['kamar' => 'Domisili dengan Wilayah, Daerah, dan Kamar ini sudah ada.'])->withInput();
        }

        Kamar::create([
            'nomor_kamar' => $request->kamar,
            'daerah_id' => $daerah->id
        ]);

        return redirect()->route('master.domisili.index')->with('success', 'Data Domisili berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $userLevel = Auth::user()->level;
        abort_if(!in_array($userLevel, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $kamarModel = Kamar::with('daerah.wilayah')->findOrFail($id);
        $domisili = (object) [
            'id' => $kamarModel->id,
            'wilayah' => $kamarModel->daerah->wilayah->nama_wilayah ?? '',
            'daerah' => $kamarModel->daerah->nama_daerah ?? '',
            'entitas_daerah' => $kamarModel->daerah->entitas_daerah ?? '',
            'kamar' => $kamarModel->nomor_kamar
        ];
        return view('master.domisili.edit', compact('domisili'));
    }

    public function update(Request $request, $id)
    {
        $userLevel = Auth::user()->level;
        abort_if(!in_array($userLevel, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $kamarModel = Kamar::findOrFail($id);

        $wilayah = Wilayah::firstOrCreate(['nama_wilayah' => $request->wilayah]);
        $daerah = Daerah::where('nama_daerah', $request->daerah)
            ->where('wilayah_id', $wilayah->id)
            ->first();

        if (!$daerah) {
            $daerah = Daerah::create([
                'nama_daerah' => $request->daerah,
                'wilayah_id' => $wilayah->id,
                'entitas_daerah' => $request->entitas_daerah
            ]);
        } elseif ($request->filled('entitas_daerah') && $daerah->entitas_daerah !== $request->entitas_daerah) {
            $daerah->update(['entitas_daerah' => $request->entitas_daerah]);
        }

        // Cek duplikasi selain id ini
        $exists = Kamar::where('nomor_kamar', $request->kamar)
            ->where('daerah_id', $daerah->id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['kamar' => 'Domisili dengan Wilayah, Daerah, dan Kamar ini sudah ada.'])->withInput();
        }

        $kamarModel->update([
            'nomor_kamar' => $request->kamar,
            'daerah_id' => $daerah->id
        ]);

        return redirect()->route('master.domisili.index')->with('success', 'Data Domisili berhasil diupdate.');
    }

    public function destroy($id)
    {
        $userLevel = Auth::user()->level;
        abort_if(!in_array($userLevel, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $kamarModel = Kamar::findOrFail($id);
        $kamarModel->delete();

        return redirect()->route('master.domisili.index')->with('success', 'Data Domisili berhasil dihapus.');
    }
}
