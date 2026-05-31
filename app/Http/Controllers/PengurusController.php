<?php

namespace App\Http\Controllers;

use App\Models\Domisili;
use App\Models\Pendidikan;
use App\Models\Pengurus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengurusExport;
use App\Models\Angkatan;
use App\Models\MasterTugas;
use App\Models\MasterStrukturJabatan;
use Illuminate\Http\Request;


class PengurusController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Pengurus::with(['domisili', 'strukturJabatan', 'tugas']);

        // FILTER PERAN
        if ($user->isWilayah()) {
            $query->whereHas('domisili', function($q) use ($user) { $q->where('wilayah', $user->wilayah); });
        } elseif ($user->isDaerah()) {
            $query->whereHas('domisili', function($q) use ($user) { $q->where('daerah', $user->daerah); });
        }

        // FILTER PERMINTAAN
        if ($request->filled('wilayah') && ($user->isAdmin() || $user->isBiktren())) {
            $query->whereHas('domisili', function($q) use ($request) { $q->where('wilayah', $request->wilayah); });
        }
        if ($request->filled('daerah')) {
            $query->whereHas('domisili', function($q) use ($request) { $q->where('daerah', $request->daerah); });
        }
        if ($request->filled('entitas_daerah_id')) $query->where('entitas_daerah', $request->entitas_daerah_id);
        if ($request->filled('entitas')) {
            $query->whereHas('strukturJabatan', function($q) use ($request) {
                $q->where('entitas', $request->entitas);
            });
        }
        if ($request->filled('jabatan')) {
            $query->whereHas('strukturJabatan', function($q) use ($request) {
                $q->where('jabatan', $request->jabatan);
            });
        }

        // FILTER JENIS TUGAS
        if ($request->filled('jenis_tugas')) {
            $query->whereHas('tugas', function ($q) use ($request) {
                $q->where('jenis_tugas', $request->jenis_tugas);
            });
        }

        // FILTER SPECIFIC TUGAS
        if ($request->filled('tugas')) {
            $query->whereHas('fungsionalTugas', function ($q) use ($request) {
                $q->where('master_tugas.id_tugas', $request->tugas);
            });
        }
        if ($request->filled('internal')) {
            $query->whereHas('internalTugas', function ($q) use ($request) {
                $q->where('master_tugas.id_tugas', $request->internal);
            });
        }
        if ($request->filled('eksternal')) {
            $query->whereHas('eksternalTugas', function ($q) use ($request) {
                $q->where('master_tugas.id_tugas', $request->eksternal);
            });
        }

        if ($request->filled('pendidikan')) $query->where('pendidikan_id', $request->pendidikan);
        if ($request->filled('angkatan')) $query->where('angkatan_id', $request->angkatan);
        if ($request->filled('status')) $query->where('status', $request->status);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('nama', 'like', "%{$q}%")
                    ->orWhere('niup', 'like', "%{$q}%");
            });
        }

        $pengurus = $query->orderBy('nama', 'ASC')
            ->paginate(12)
            ->appends($request->all());

        // DATA MASTER
        $wilayahList = Domisili::select('wilayah')->distinct()->orderBy('wilayah')->pluck('wilayah');
        $daerahList  = $request->filled('wilayah')
            ? Domisili::where('wilayah', $request->wilayah)->select('daerah')->distinct()->orderBy('daerah')->pluck('daerah')
            : Domisili::select('daerah')->distinct()->orderBy('daerah')->pluck('daerah');

        $fungsionalList = MasterTugas::where('jenis_tugas', 'fungsional')->orderBy('nama_tugas')->get();
        $internalList   = MasterTugas::where('jenis_tugas', 'internal')->orderBy('nama_tugas')->get();
        $eksternalList  = MasterTugas::where('jenis_tugas', 'eksternal')->orderBy('nama_tugas')->get();
        $pendidikanList = Pendidikan::orderBy('nama_pendidikan')->get();
        $angkatanList   = Angkatan::orderBy('angkatan')->get();
        $entitasList    = MasterStrukturJabatan::select('entitas')->distinct()->orderBy('entitas')->get();
        $jabatanList    = MasterStrukturJabatan::select('jabatan')->distinct()->orderBy('jabatan')->get();
        $entitasDaerahList = Domisili::whereNotNull('entitas_daerah')->select('entitas_daerah')->distinct()->orderBy('entitas_daerah')->pluck('entitas_daerah');

        return view('pokok.pengurus.index', compact(
            'pengurus',
            'wilayahList',
            'daerahList',
            'fungsionalList',
            'internalList',
            'eksternalList',
            'pendidikanList',
            'angkatanList',
            'entitasList',
            'jabatanList',
            'entitasDaerahList'
        ));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isDaerah()) abort(403, 'Maaf, akun Daerah tidak diizinkan menambah data pengurus.');

        return view('pokok.pengurus.create', [
            'wilayahs'          => Domisili::select('wilayah')->distinct()->orderBy('wilayah')->pluck('wilayah'),
            'entitasList'       => MasterStrukturJabatan::select('entitas')->distinct()->orderBy('entitas')->get(),
            'fungsionalTugas'   => MasterTugas::where('jenis_tugas', 'fungsional')->orderBy('nama_tugas')->get(),
            'rangkapInternals'  => MasterTugas::where('jenis_tugas', 'internal')->orderBy('nama_tugas')->get(),
            'rangkapEksternals' => MasterTugas::where('jenis_tugas', 'eksternal')->orderBy('nama_tugas')->get(),
            'pendidikans'       => Pendidikan::orderBy('nama_pendidikan')->get(),
            'angkatans'         => Angkatan::orderBy('angkatan')->get(),
            'entitasDaerahs'    => Domisili::whereNotNull('entitas_daerah')->select('entitas_daerah')->distinct()->orderBy('entitas_daerah')->pluck('entitas_daerah'),
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isDaerah()) abort(403, 'Akses ditolak.');

        $request->validate([
            'niup'                 => 'required|string|max:50|unique:penguruses,niup',
            'nama'                 => 'required|string|max:255',
            'domisili_id'          => 'required|exists:domisilis,id',
            'entitas_daerah'       => 'nullable|string',
            'struktur_jabatan_id'  => 'required|exists:master_struktur_jabatans,id',
            'foto'                 => 'nullable|image|mimes:jpeg,png,jpg|max:15360|dimensions:min_width=1080,min_height=1080',
        ], [
            'foto.max'        => 'Ukuran foto terlalu besar. Maksimal 15 MB.',
            'foto.dimensions' => 'Resolusi foto kurang tajam. Minimal dimensi gambar adalah 1080 x 1080 piksel.',
            'foto.image'      => 'File yang diunggah harus berupa file gambar.',
            'foto.mimes'      => 'Format foto hanya boleh JPG, JPEG, atau PNG.',
        ]);

        // LOGIK LOKASI
        $domisiliId = $request->domisili_id;
        if (!$domisiliId) {
            return back()->withErrors(['domisili_id' => 'Kamar/Domisili wajib diisi.'])->withInput();
        }

        // UPLOAD FOTO
        $fotoPath = $request->hasFile('foto') ? $request->file('foto')->store('uploads/pengurus/foto', 'public') : null;

        $skPath = null;
        $suratPath = null;
        $pltPath = null;
        $lainPath = null;
        if ($user->isAdmin() || $user->isBiktren()) {
            $skPath    = $request->file('berkas_sk_pengurus') ? $request->file('berkas_sk_pengurus')->store('uploads/pengurus/berkas_sk', 'public') : null;
            $suratPath = $request->file('berkas_surat_tugas') ? $request->file('berkas_surat_tugas')->store('uploads/pengurus/berkas_surat', 'public') : null;
            $pltPath   = $request->file('berkas_plt') ? $request->file('berkas_plt')->store('uploads/pengurus/berkas_plt', 'public') : null;
            $lainPath  = $request->file('berkas_lain') ? $request->file('berkas_lain')->store('uploads/pengurus/berkas_lain', 'public') : null;
        }

        // SIMPAN DATA
        $pengurus = Pengurus::create([
            'niup'                 => $request->niup,
            'nama'                 => $request->nama,
            'domisili_id'          => $domisiliId,
            'entitas_daerah'       => $request->entitas_daerah,
            'struktur_jabatan_id'  => $request->struktur_jabatan_id,
            'sk_kepengurusan'      => $request->sk_kepengurusan,
            'pendidikan_id'        => $request->pendidikan_id,
            'angkatan_id'          => $request->angkatan_id,
            'status'               => 'aktif', // Default Aktif
            'foto'                 => $fotoPath,
            'berkas_sk_pengurus'   => $skPath,
            'berkas_surat_tugas'   => $suratPath,
            'berkas_plt'           => $pltPath,
            'berkas_lain'          => $lainPath,
        ]);

        // LOGIK TUGAS MULTI-SELECT & SINGLE-SELECT
        $tugasArray = $request->input('tugas', []);
        
        if ($request->filled('tugas_internal_id')) {
            $tugasArray[] = ['id' => $request->tugas_internal_id, 'status' => 'aktif'];
        }
        if ($request->filled('tugas_eksternal_id')) {
            $tugasArray[] = ['id' => $request->tugas_eksternal_id, 'status' => 'aktif'];
        }

        if (!empty($tugasArray)) {
            foreach ($tugasArray as $item) {
                if (isset($item['id'])) {
                    $tugasId = $item['id'];
                    $status  = $item['status'];

                    // A. Simpan ke Pivot
                    $pengurus->tugas()->attach($tugasId, ['status' => $status]);


                }
            }
        }

        return redirect()->route('pokok.pengurus.index')
            ->with('success', 'Data pengurus berhasil ditambahkan!');
    }

    public function show($id)
    {
        $pengurus = Pengurus::with([
            'domisili',
            'strukturJabatan',
            'tugas',
            'pendidikan',
            'angkatan',
        ])->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isWilayah() && $pengurus->domisili?->wilayah != $user->wilayah) abort(403);
        if ($user->isDaerah() && $pengurus->domisili?->daerah != $user->daerah) abort(403);

        return view('pokok.pengurus.show', compact('pengurus'));
    }

    public function edit($id)
    {
        $pengurus = Pengurus::with('tugas')->findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isWilayah() && $pengurus->domisili?->wilayah != $user->wilayah) abort(403);
        if ($user->isDaerah() && $pengurus->domisili?->daerah != $user->daerah) abort(403);

        return view('pokok.pengurus.edit', [
            'pengurus'          => $pengurus,
            'wilayahs'          => Domisili::select('wilayah')->distinct()->orderBy('wilayah')->pluck('wilayah'),
            'daerahs'           => Domisili::where('wilayah', $pengurus->domisili?->wilayah)->select('daerah')->distinct()->orderBy('daerah')->pluck('daerah'),
            'kamars'            => Domisili::where('daerah', $pengurus->domisili?->daerah)->orderBy('kamar')->get(),
            'entitasList'       => MasterStrukturJabatan::select('entitas')->distinct()->orderBy('entitas')->get(),
            'jabatans'          => MasterStrukturJabatan::where('entitas', $pengurus->strukturJabatan?->entitas)->select('jabatan')->distinct()->get(),
            'jenis_jabatans'    => MasterStrukturJabatan::where('entitas', $pengurus->strukturJabatan?->entitas)->where('jabatan', $pengurus->strukturJabatan?->jabatan)->select('jenis_jabatan')->distinct()->get(),
            'grade_jabatans'    => MasterStrukturJabatan::where('entitas', $pengurus->strukturJabatan?->entitas)->where('jabatan', $pengurus->strukturJabatan?->jabatan)->where('jenis_jabatan', $pengurus->strukturJabatan?->jenis_jabatan)->get(),
            'fungsionalTugas'   => MasterTugas::where('jenis_tugas', 'fungsional')->orderBy('nama_tugas')->get(),
            'rangkapInternals'  => MasterTugas::where('jenis_tugas', 'internal')->orderBy('nama_tugas')->get(),
            'rangkapEksternals' => MasterTugas::where('jenis_tugas', 'eksternal')->orderBy('nama_tugas')->get(),
            'pendidikans'       => Pendidikan::orderBy('nama_pendidikan')->get(),
            'angkatans'         => Angkatan::orderBy('angkatan')->get(),
            'entitasDaerahs'    => Domisili::whereNotNull('entitas_daerah')->select('entitas_daerah')->distinct()->orderBy('entitas_daerah')->pluck('entitas_daerah'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $pengurus = Pengurus::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isWilayah() && $pengurus->domisili?->wilayah != $user->wilayah) abort(403);
        if ($user->isDaerah() && $pengurus->domisili?->daerah != $user->daerah) abort(403);

        $request->validate([
            'niup'   => 'required',
            'nama'   => 'required',
            'foto'   => 'nullable|image|mimes:jpeg,png,jpg|max:15360|dimensions:min_width=1080,min_height=1080',
        ], [
            'foto.max'        => 'Ukuran foto terlalu besar. Maksimal 15 MB.',
            'foto.dimensions' => 'Resolusi foto kurang tajam. Minimal dimensi gambar adalah 1080 x 1080 piksel.',
            'foto.image'      => 'File yang diunggah harus berupa file gambar.',
            'foto.mimes'      => 'Format foto hanya boleh JPG, JPEG, atau PNG.',
        ]);

        // 1. UPDATE DATA UTAMA
        $pengurus->niup                 = $request->niup;
        $pengurus->nama                 = $request->nama;
        $pengurus->domisili_id          = $request->domisili_id ?? $pengurus->domisili_id;
        $pengurus->entitas_daerah       = $request->entitas_daerah;
        $pengurus->struktur_jabatan_id  = $request->struktur_jabatan_id;
        $pengurus->pendidikan_id        = $request->pendidikan_id;
        $pengurus->angkatan_id          = $request->angkatan_id;
        $pengurus->sk_kepengurusan      = $request->sk_kepengurusan;

        // Ambil status jika ada di request (dari form edit), jika tidak pakai status lama
        if ($request->has('status')) {
            $pengurus->status = $request->status;
        }

        if ($user->isAdmin() || $user->isBiktren() || $user->isWilayah()) {
            if ($request->filled('domisili_id')) {
                $pengurus->domisili_id = $request->domisili_id;
            }
        }

        // UPDATE FOTO
        if ($request->hasFile('foto')) {
            if ($pengurus->foto && Storage::disk('public')->exists($pengurus->foto)) {
                Storage::disk('public')->delete($pengurus->foto);
            }
            $pengurus->foto = $request->file('foto')->store('uploads/pengurus/foto', 'public');
        }

        // UPDATE BERKAS
        if ($user->isAdmin() || $user->isBiktren()) {
            $fileFields = ['berkas_sk_pengurus', 'berkas_surat_tugas', 'berkas_plt', 'berkas_lain'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    if ($pengurus->{$field} && Storage::disk('public')->exists($pengurus->{$field})) {
                        Storage::disk('public')->delete($pengurus->{$field});
                    }
                    $pengurus->{$field} = $request->file($field)->store("uploads/pengurus/{$field}", 'public');
                }
            }
        }

        $pengurus->save();

        // 2. LOGIKA SINKRONISASI TUGAS (RELASI)
        $pengurus->tugas()->detach();

        // Ambil input tugas dari form
        $tugasArray = $request->input('tugas', []);
        if ($request->filled('tugas_internal_id')) {
            $tugasArray[] = ['id' => $request->tugas_internal_id, 'status' => 'aktif'];
        }
        if ($request->filled('tugas_eksternal_id')) {
            $tugasArray[] = ['id' => $request->tugas_eksternal_id, 'status' => 'aktif'];
        }
        $inputTugas = collect($tugasArray);
        $allMasterTugas = MasterTugas::all();

        foreach ($allMasterTugas as $master) {
            $namaTugas = strtolower($master->nama_tugas);

            // Cek apakah tugas ini ada di inputan user (dicentang)?
            $selectedItem = $inputTugas->firstWhere('id', $master->id_tugas);
            $isDipilih    = !is_null($selectedItem);

            // LOGIKA BARU: Jika Pengurus Utama NON-AKTIF -> Paksa Tugas NON-AKTIF
            if ($pengurus->status == 'non_aktif') {
                $statusTugas = 'non_aktif';
            } else {
                // Jika Pengurus Aktif -> Ikuti inputan dropdown tugas (default non_aktif jika tak dipilih)
                $statusTugas = $isDipilih ? $selectedItem['status'] : 'non_aktif';
            }

            // SIMPAN KE PIVOT (Hanya jika dicentang oleh user)
            if ($isDipilih) {
                $pengurus->tugas()->attach($master->id_tugas, ['status' => $statusTugas]);
            }


        }

        return redirect()->route('pokok.pengurus.index')
            ->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengurus = Pengurus::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isDaerah()) abort(403);
        if ($user->isWilayah() && $pengurus->wilayah_id != $user->wilayah_id) abort(403);

        $fileFields = ['foto', 'berkas_sk_pengurus', 'berkas_surat_tugas', 'berkas_plt', 'berkas_lain'];
        foreach ($fileFields as $field) {
            if ($pengurus->{$field} && Storage::disk('public')->exists($pengurus->{$field})) {
                Storage::disk('public')->delete($pengurus->{$field});
            }
        }

        $pengurus->delete();

        return redirect()->route('pokok.pengurus.index')
            ->with('success', 'Data pengurus berhasil dihapus.');
    }

    public function export(Request $request)
    {
        return Excel::download(new PengurusExport($request), 'pengurus.xlsx');
    }
}
