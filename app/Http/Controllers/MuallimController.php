<?php

namespace App\Http\Controllers;

use App\Models\Pengurus;
use Illuminate\Http\Request;

class MuallimController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $muallim = \App\Models\Pengurus::with(['fungsionalTugas'])
            ->whereHas('fungsionalTugas', function ($q) {
                $q->where('tugas', "Mu'allim");
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%$search%")
                        ->orWhere('niup', 'like', "%$search%");
                });
            })
            ->orderBy('nama', 'asc')
            ->get();

        return view('pokok.muallim.index', compact('muallim', 'search'));
    }
}
