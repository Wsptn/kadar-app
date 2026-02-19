<?php

namespace App\Http\Controllers;

use App\Models\Pengurus;
use App\Models\WaliAsuh;
use Illuminate\Http\Request;

class WaliAsuhController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $waliasuh = \App\Models\Pengurus::with(['fungsionalTugas'])
            ->whereHas('fungsionalTugas', function ($q) {
                $q->where('tugas', "Wali Asuh");
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%$search%")
                        ->orWhere('niup', 'like', "%$search%");
                });
            })
            ->orderBy('nama', 'asc')
            ->paginate(12);

        return view('pokok.waliasuh.index', compact('waliasuh', 'search'));
    }
}
