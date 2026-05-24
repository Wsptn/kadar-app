<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Models\Daerah;
use App\Models\MasterStrukturJabatan;
use App\Models\Kamar;
use App\Models\MasterTugas;
use App\Models\EntitasDaerah;
use App\Models\Pendidikan;
use App\Models\Pengurus;
use App\Models\Wilayah;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengurusExport;
use Illuminate\Http\Request;

// Model Otomatisasi
use App\Models\Muallim;
use App\Models\WaliAsuh;
use App\Models\Pengajar;

class PengurusController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Pengurus::with(['wilayah', 'daerah', 'kamar', 'strukturJabatan', 'tugas']);

        // FILTER PERAN
        if ($user->isWilayah()) {
            $query->where('wilayah_id', $user->wilayah_id);
        } elseif ($user->isDaerah()) {
            $query->where('daerah_id', $user->daerah_id);
        }

        // FILTER PERMINTAAN
        if ($request->filled('wilayah') && ($user->isAdmin() || $user->isBiktren())) {
            $query->where('wilayah_id', $request->wilayah);
        }
        if ($request->filled('daerah')) $query->where('daerah_id', $request->daerah);
        if ($request->filled('entitas_daerah_id')) $query->where('entitas_daerah_id', $request->entitas_daerah_id);
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
        $wilayahList = Wilayah::orderBy('nama_wilayah')->get();
        $daerahList  = $request->filled('wilayah')
            ? Daerah::where('wilayah_id', $request->wilayah)->orderBy('nama_daerah')->get()
            : Daerah::orderBy('nama_daerah')->get();

        $fungsionalList = MasterTugas::where('jenis_tugas', 'fungsional')->orderBy('nama_tugas')->get();
        $internalList   = MasterTugas::where('jenis_tugas', 'internal')->orderBy('nama_tugas')->get();
        $eksternalList  = MasterTugas::where('jenis_tugas', 'eksternal')->orderBy('nama_tugas')->get();
        $pendidikanList = Pendidikan::orderBy('nama_pendidikan')->get();
        $angkatanList   = Angkatan::orderBy('angkatan')->get();
        $entitasList    = MasterStrukturJabatan::select('entitas')->distinct()->orderBy('entitas')->get();
        $jabatanList    = MasterStrukturJabatan::select('jabatan')->distinct()->orderBy('jabatan')->get();
        $entitasDaerahList = EntitasDaerah::orderBy('nama_entitas_daerah')->get();

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
            'wilayahs'          => Wilayah::orderBy('nama_wilayah')->get(),
            'entitasList'       => MasterStrukturJabatan::select('entitas')->distinct()->orderBy('entitas')->get(),
            'fungsionalTugas'   => MasterTugas::where('jenis_tugas', 'fungsional')->orderBy('nama_tugas')->get(),
            'rangkapInternals'  => MasterTugas::where('jenis_tugas', 'internal')->orderBy('nama_tugas')->get(),
            'rangkapEksternals' => MasterTugas::where('jenis_tugas', 'eksternal')->orderBy('nama_tugas')->get(),
            'pendidikans'       => Pendidikan::orderBy('nama_pendidikan')->get(),
            'angkatans'         => Angkatan::orderBy('angkatan')->get(),
            'entitasDaerahs'    => EntitasDaerah::orderBy('nama_entitas_daerah')->get(),
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
            'wilayah_id'           => 'required',
            'daerah_id'            => 'required',
            'kamar_id'             => 'required|exists:kamars,id',
            'struktur_jabatan_id'  => 'required|exists:master_struktur_jabatans,id',
            'foto'                 => 'nullable|image|mimes:jpeg,png,jpg|max:15360|dimensions:min_width=1080,min_height=1080',
        ], [
            'foto.max'        => 'Ukuran foto terlalu besar. Maksimal 15 MB.',
            'foto.dimensions' => 'Resolusi foto kurang tajam. Minimal dimensi gambar adalah 1080 x 1080 piksel.',
            'foto.image'      => 'File yang diunggah harus berupa file gambar.',
            'foto.mimes'      => 'Format foto hanya boleh JPG, JPEG, atau PNG.',
        ]);

        // LOGIK LOKASI
        $wilayahId = $request->wilayah_id;
        $daerahId  = $request->daerah_id;
        if ($user->isWilayah()) {
            $wilayahId = $user->wilayah_id;
            $daerahId  = $request->daerah_id;
        }
        if (!$wilayahId || !$daerahId) {
            return back()->withErrors(['daerah_id' => 'Wilayah dan Daerah wajib diisi.'])->withInput();
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
            'wilayah_id'           => $wilayahId,
            'daerah_id'            => $daerahId,
            'entitas_daerah_id'    => $request->entitas_daerah_id,
            'kamar_id'             => $request->kamar_id,
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

                    // B. Otomatisasi (Copy Nama & NIUP) khusus untuk fungsional tertentu
                    $masterTugas = MasterTugas::where('id_tugas', $tugasId)->first();
                    if ($masterTugas) {
                        $namaTugas = strtolower($masterTugas->nama_tugas);
                        $dataLengkap = [
                            'status' => 'aktif',
                            'nama'   => $pengurus->nama,
                            'niup'   => $pengurus->niup
                        ];

                        if ($namaTugas === "mu'allim" || $namaTugas === "muallim") {
                            if ($status == 'aktif') Muallim::firstOrCreate(['pengurus_id' => $pengurus->id], $dataLengkap);
                        }
                        if ($namaTugas === "wali asuh") {
                            if ($status == 'aktif') WaliAsuh::firstOrCreate(['pengurus_id' => $pengurus->id], $dataLengkap);
                        }
                        if ($namaTugas === "pengajar") {
                            if ($status == 'aktif') Pengajar::firstOrCreate(['pengurus_id' => $pengurus->id], $dataLengkap);
                        }
                    }
                }
            }
        }

        return redirect()->route('pokok.pengurus.index')
            ->with('success', 'Data pengurus berhasil ditambahkan!');
    }

    public function show($id)
    {
        $pengurus = Pengurus::with([
            'wilayah',
            'daerah',
            'kamar',
            'strukturJabatan',
            'tugas',
            'pendidikan',
            'angkatan',
        ])->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isWilayah() && $pengurus->wilayah_id != $user->wilayah_id) abort(403);
        if ($user->isDaerah() && $pengurus->daerah_id != $user->daerah_id) abort(403);

        return view('pokok.pengurus.show', compact('pengurus'));
    }

    public function edit($id)
    {
        $pengurus = Pengurus::with('tugas')->findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isWilayah() && $pengurus->wilayah_id != $user->wilayah_id) abort(403);
        if ($user->isDaerah() && $pengurus->daerah_id != $user->daerah_id) abort(403);

        return view('pokok.pengurus.edit', [
            'pengurus'          => $pengurus,
            'wilayahs'          => Wilayah::orderBy('nama_wilayah')->get(),
            'daerahs'           => Daerah::where('wilayah_id', $pengurus->wilayah_id)->orderBy('nama_daerah')->get(),
            'kamars'            => Kamar::where('daerah_id', $pengurus->daerah_id)->orderBy('id')->get(),
            'entitasList'       => MasterStrukturJabatan::select('entitas')->distinct()->orderBy('entitas')->get(),
            'jabatans'          => MasterStrukturJabatan::where('entitas', $pengurus->strukturJabatan?->entitas)->select('jabatan')->distinct()->get(),
            'jenis_jabatans'    => MasterStrukturJabatan::where('entitas', $pengurus->strukturJabatan?->entitas)->where('jabatan', $pengurus->strukturJabatan?->jabatan)->select('jenis_jabatan')->distinct()->get(),
            'grade_jabatans'    => MasterStrukturJabatan::where('entitas', $pengurus->strukturJabatan?->entitas)->where('jabatan', $pengurus->strukturJabatan?->jabatan)->where('jenis_jabatan', $pengurus->strukturJabatan?->jenis_jabatan)->get(),
            'fungsionalTugas'   => MasterTugas::where('jenis_tugas', 'fungsional')->orderBy('nama_tugas')->get(),
            'rangkapInternals'  => MasterTugas::where('jenis_tugas', 'internal')->orderBy('nama_tugas')->get(),
            'rangkapEksternals' => MasterTugas::where('jenis_tugas', 'eksternal')->orderBy('nama_tugas')->get(),
            'pendidikans'       => Pendidikan::orderBy('nama_pendidikan')->get(),
            'angkatans'         => Angkatan::orderBy('angkatan')->get(),
            'entitasDaerahs'    => EntitasDaerah::orderBy('nama_entitas_daerah')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $pengurus = Pengurus::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isWilayah() && $pengurus->wilayah_id != $user->wilayah_id) abort(403);
        if ($user->isDaerah() && $pengurus->daerah_id != $user->daerah_id) abort(403);

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
        $pengurus->kamar_id             = $request->kamar_id;
        $pengurus->entitas_daerah_id    = $request->entitas_daerah_id;
        $pengurus->struktur_jabatan_id  = $request->struktur_jabatan_id;
        $pengurus->pendidikan_id        = $request->pendidikan_id;
        $pengurus->angkatan_id          = $request->angkatan_id;
        $pengurus->sk_kepengurusan      = $request->sk_kepengurusan;

        // Ambil status jika ada di request (dari form edit), jika tidak pakai status lama
        if ($request->has('status')) {
            $pengurus->status = $request->status;
        }

        if ($user->isAdmin() || $user->isBiktren()) {
            $pengurus->wilayah_id = $request->wilayah_id;
            $pengurus->daerah_id  = $request->daerah_id;
        } elseif ($user->isWilayah()) {
            $pengurus->daerah_id = $request->daerah_id;
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

            // 3. SINKRONISASI KE TABEL SPESIFIK (Muallim/WaliAsuh)
            $dataSync = [
                'status' => $statusTugas,
                'nama'   => $pengurus->nama,
                'niup'   => $pengurus->niup
            ];

            // --- MU'ALLIM ---
            if ($namaTugas === "mu'allim" || $namaTugas === "muallim") {
                if ($isDipilih) {
                    Muallim::updateOrCreate(['pengurus_id' => $pengurus->id], $dataSync);
                } else {
                    Muallim::where('pengurus_id', $pengurus->id)->update(['status' => 'non_aktif']);
                }
            }

            // --- WALI ASUH ---
            if ($namaTugas === "wali asuh") {
                if ($isDipilih) {
                    WaliAsuh::updateOrCreate(['pengurus_id' => $pengurus->id], $dataSync);
                } else {
                    WaliAsuh::where('pengurus_id', $pengurus->id)->update(['status' => 'non_aktif']);
                }
            }

            // --- PENGAJAR ---
            if ($namaTugas === "pengajar") {
                if ($isDipilih) {
                    Pengajar::updateOrCreate(['pengurus_id' => $pengurus->id], $dataSync);
                } else {
                    Pengajar::where('pengurus_id', $pengurus->id)->update(['status' => 'non_aktif']);
                }
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
