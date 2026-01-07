<?php

namespace App\Http\Controllers;

use App\Models\Pengurus;
use Illuminate\Http\Request;

class PengajarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pengajar = \App\Models\Pengurus::with(['fungsionalTugas'])
            ->whereHas('fungsionalTugas', function ($q) {
                $q->where('tugas', "Pengajar");
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%$search%")
                        ->orWhere('niup', 'like', "%$search%");
                });
            })
            ->orderBy('nama', 'asc')
            ->get();

        return view('pokok.pengajar.index', compact('pengajar', 'search'));
    }
}
