<?php

namespace App\Http\Controllers;

use App\Models\Domisili;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DomisiliController extends Controller
{
    public function index()
    {
        $userLevel = Auth::user()->level;
        $hasAccess = $userLevel == 'Admin' || $userLevel == 'Wilayah';

        $domisilis = Domisili::orderBy('wilayah')
            ->orderBy('daerah')
            ->orderBy('kamar')
            ->get();

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

        // Cek duplikasi
        $exists = Domisili::where('wilayah', $request->wilayah)
            ->where('daerah', $request->daerah)
            ->where('kamar', $request->kamar)
            ->exists();

        if ($exists) {
            return back()->withErrors(['kamar' => 'Domisili dengan Wilayah, Daerah, dan Kamar ini sudah ada.'])->withInput();
        }

        Domisili::create($request->only('wilayah', 'daerah', 'entitas_daerah', 'kamar'));

        return redirect()->route('master.domisili.index')->with('success', 'Data Domisili berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $userLevel = Auth::user()->level;
        abort_if(!in_array($userLevel, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $domisili = Domisili::findOrFail($id);
        return view('master.domisili.edit', compact('domisili'));
    }

    public function update(Request $request, $id)
    {
        $userLevel = Auth::user()->level;
        abort_if(!in_array($userLevel, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $domisili = Domisili::findOrFail($id);

        $request->validate([
            'wilayah' => 'required|string|max:255',
            'daerah'  => 'required|string|max:255',
            'entitas_daerah' => 'nullable|string|max:255',
            'kamar'   => 'required|string|max:255',
        ]);

        // Cek duplikasi selain id ini
        $exists = Domisili::where('wilayah', $request->wilayah)
            ->where('daerah', $request->daerah)
            ->where('kamar', $request->kamar)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['kamar' => 'Domisili dengan Wilayah, Daerah, dan Kamar ini sudah ada.'])->withInput();
        }

        $domisili->update($request->only('wilayah', 'daerah', 'entitas_daerah', 'kamar'));

        return redirect()->route('master.domisili.index')->with('success', 'Data Domisili berhasil diupdate.');
    }

    public function destroy($id)
    {
        $userLevel = Auth::user()->level;
        abort_if(!in_array($userLevel, ['Admin', 'Wilayah']), 403, 'Unauthorized action.');

        $domisili = Domisili::findOrFail($id);
        $domisili->delete();

        return redirect()->route('master.domisili.index')->with('success', 'Data Domisili berhasil dihapus.');
    }
}
