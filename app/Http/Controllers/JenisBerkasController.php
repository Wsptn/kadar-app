<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisBerkas;
use Illuminate\Http\Request;

class JenisBerkasController extends Controller
{
    public function index()
    {
        $jenisBerkas = MasterJenisBerkas::all();

        return view('master.berkas.index', compact('jenisBerkas'));
    }
}
