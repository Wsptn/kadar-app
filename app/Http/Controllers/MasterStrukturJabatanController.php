<?php

namespace App\Http\Controllers;

use App\Models\MasterStrukturJabatan;
use Illuminate\Http\Request;
use App\Imports\JabatanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class MasterStrukturJabatanController extends Controller
{
    public function index()
    {
        $jabatans = MasterStrukturJabatan::orderBy('entitas')
            ->orderBy('jabatan')
            ->orderBy('jenis_jabatan')
            ->orderBy('grade')
            ->get();
        return view('master.struktur_jabatan.index', compact('jabatans'));
    }

    public function create()
    {
        abort_if(auth()->user()->level === 'Daerah', 403, 'Unauthorized action.');
        return view('master.struktur_jabatan.create');
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->level === 'Daerah', 403, 'Unauthorized action.');
        $request->validate([
            'entitas' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'jenis_jabatan' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
        ]);

        MasterStrukturJabatan::create($request->all());

        return redirect()->route('master.struktur_jabatan.index')
            ->with('success', 'Data Struktur Jabatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        abort_if(auth()->user()->level === 'Daerah', 403, 'Unauthorized action.');
        $jabatan = MasterStrukturJabatan::findOrFail($id);
        return view('master.struktur_jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        abort_if(auth()->user()->level === 'Daerah', 403, 'Unauthorized action.');
        $request->validate([
            'entitas' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'jenis_jabatan' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
        ]);

        $jabatan = MasterStrukturJabatan::findOrFail($id);
        $jabatan->update($request->all());

        return redirect()->route('master.struktur_jabatan.index')
            ->with('success', 'Data Struktur Jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        abort_if(auth()->user()->level === 'Daerah', 403, 'Unauthorized action.');
        $jabatan = MasterStrukturJabatan::findOrFail($id);
        $jabatan->delete();

        return redirect()->route('master.struktur_jabatan.index')
            ->with('success', 'Data Struktur Jabatan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        abort_if(!Auth::user()->isAdmin(), 403, 'Unauthorized action.');

        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new JabatanImport();
            Excel::import($import, $request->file('file'));
            
            $msg = 'Data Jabatan berhasil diimport. ';
            if ($import->successRows > 0) {
                $msg .= $import->successRows . ' data jabatan baru ditambahkan.';
            } else {
                $msg .= 'Semua data jabatan di file sudah ada di sistem.';
            }
            
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        abort_if(!Auth::user()->isAdmin(), 403, 'Unauthorized action.');
        
        $filePath = public_path('template_import_jabatan.xlsx');
        
        if (!file_exists($filePath)) {
            $export = new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function array(): array {
                    return [
                        ['Pusat', 'Ketua Umum', 'BPH', 'A'],
                        ['Wilayah', 'Ketua Wilayah', 'Struktural', 'B']
                    ];
                }
                public function headings(): array {
                    return ['Entitas', 'Jabatan', 'Jenis Jabatan', 'Grade'];
                }
            };
            return Excel::download($export, 'template_import_jabatan.xlsx');
        }

        return response()->download($filePath);
    }
}
