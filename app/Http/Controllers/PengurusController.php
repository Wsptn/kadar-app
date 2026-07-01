<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\Daerah;
use App\Models\Kamar;
use App\Models\Pendidikan;
use App\Models\Pengurus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengurusExport;
use App\Imports\PengurusImport;
use App\Models\MasterTugas;
use App\Models\MasterStrukturJabatan;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatTugas;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\Request;

class PengurusController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Pengurus::with(['kamar.daerah.wilayah', 'strukturJabatan', 'tugas']);

        // FILTER PERAN
        if ($user->isWilayah()) {
            $query->whereHas('kamar.daerah.wilayah', function($q) use ($user) { $q->where('nama_wilayah', $user->wilayah); });
        } elseif ($user->isDaerah()) {
            $query->whereHas('kamar.daerah', function($q) use ($user) { $q->where('nama_daerah', $user->daerah); });
        }

        // FILTER PERMINTAAN
        if ($request->filled('wilayah') && ($user->isAdmin() || $user->isBiktren())) {
            $query->whereHas('kamar.daerah.wilayah', function($q) use ($request) { $q->where('nama_wilayah', $request->wilayah); });
        }
        if ($request->filled('daerah')) {
            $query->whereHas('kamar.daerah', function($q) use ($request) { $q->where('nama_daerah', $request->daerah); });
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
                $q->where('tugas.id', $request->tugas);
            });
        }
        if ($request->filled('internal')) {
            $query->whereHas('internalTugas', function ($q) use ($request) {
                $q->where('tugas.id', $request->internal);
            });
        }
        if ($request->filled('eksternal')) {
            $query->whereHas('eksternalTugas', function ($q) use ($request) {
                $q->where('tugas.id', $request->eksternal);
            });
        }

        if ($request->filled('pendidikan')) $query->where('pendidikan_id', $request->pendidikan);
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
        $wilayahList = Wilayah::select('nama_wilayah')->distinct()->orderBy('nama_wilayah')->pluck('nama_wilayah');
        $daerahList  = $request->filled('wilayah')
            ? Daerah::whereHas('wilayah', function($q) use ($request) { $q->where('nama_wilayah', $request->wilayah); })->select('nama_daerah')->distinct()->orderBy('nama_daerah')->pluck('nama_daerah')
            : Daerah::select('nama_daerah')->distinct()->orderBy('nama_daerah')->pluck('nama_daerah');

        $fungsionalList = MasterTugas::where('jenis_tugas', 'fungsional')->orderBy('nama_tugas')->get();
        $internalList   = MasterTugas::where('jenis_tugas', 'internal')->orderBy('nama_tugas')->get();
        $eksternalList  = MasterTugas::where('jenis_tugas', 'eksternal')->orderBy('nama_tugas')->get();
        $pendidikanList = Pendidikan::orderBy('nama_pendidikan')->get();
        $entitasList    = MasterStrukturJabatan::select('entitas')->distinct()->orderBy('entitas')->get();
        $jabatanList    = MasterStrukturJabatan::select('jabatan')->distinct()->orderBy('jabatan')->get();
        $entitasDaerahList = \App\Models\Daerah::whereNotNull('entitas_daerah')->select('entitas_daerah')->distinct()->orderBy('entitas_daerah')->pluck('entitas_daerah');

        return view('pokok.pengurus.index', compact(
            'pengurus',
            'wilayahList',
            'daerahList',
            'fungsionalList',
            'internalList',
            'eksternalList',
            'pendidikanList',
            'entitasList',
            'jabatanList',
            'entitasDaerahList'
        ));
    }

    public function arsip(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isBiktren()) abort(403, 'Akses ditolak. Hanya Admin dan Biktren yang dapat mengakses arsip.');

        $pengurus = Pengurus::onlyTrashed()->with(['kamar.daerah.wilayah', 'strukturJabatan', 'tugas', 'kinerja'])->paginate(12);

        return view('pokok.pengurus.arsip', compact('pengurus'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isDaerah()) abort(403, 'Maaf, akun Daerah tidak diizinkan menambah data pengurus.');

        return view('pokok.pengurus.create', [
            'wilayahs'          => Wilayah::select('nama_wilayah')->distinct()->orderBy('nama_wilayah')->pluck('nama_wilayah'),
            'entitasList'       => MasterStrukturJabatan::select('entitas')->distinct()->orderBy('entitas')->get(),
            'fungsionalTugas'   => MasterTugas::where('jenis_tugas', 'fungsional')->orderBy('nama_tugas')->get(),
            'rangkapInternals'  => MasterTugas::where('jenis_tugas', 'internal')->orderBy('nama_tugas')->get(),
            'rangkapEksternals' => MasterTugas::where('jenis_tugas', 'eksternal')->orderBy('nama_tugas')->get(),
            'pendidikans'       => Pendidikan::orderBy('nama_pendidikan')->get(),
            'entitasDaerahs'    => \App\Models\Daerah::whereNotNull('entitas_daerah')->select('entitas_daerah')->distinct()->orderBy('entitas_daerah')->pluck('entitas_daerah'),
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isDaerah()) abort(403, 'Akses ditolak.');

        $request->validate([
            'niup'                 => 'required|string|max:50|unique:pengurus,niup',
            'nama'                 => 'required|string|max:255',
            'domisili_id'          => 'required|exists:kamar,id',
            'entitas_daerah'       => 'nullable|string',
            'jabatan_id'           => 'required|exists:jabatan,id',
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
        $tglMulaiJabatan = $request->tgl_mulai_jabatan ?? date('Y-m-d');

        $pengurus = Pengurus::create([
            'niup'                 => $request->niup,
            'nama'                 => $request->nama,
            'kamar_id'             => $domisiliId,
            'entitas_daerah'       => $request->entitas_daerah,
            'jabatan_id'           => $request->jabatan_id,
            'sk_kepengurusan'      => $request->sk_kepengurusan,
            'pendidikan_id'        => $request->pendidikan_id,
            'status'               => 'aktif', // Default Aktif
            'tgl_mulai_tugas'      => $tglMulaiJabatan, // Fallback legacy
            'foto'                 => $fotoPath,
            'berkas_sk_pengurus'   => $skPath,
            'berkas_surat_tugas'   => $suratPath,
            'berkas_plt'           => $pltPath,
            'berkas_lain'          => $lainPath,
        ]);

        // LOGIK RIWAYAT JABATAN AWAL
        if ($request->jabatan_id) {
            RiwayatJabatan::create([
                'pengurus_id'         => $pengurus->id,
                'jabatan_id'          => $request->jabatan_id,
                'tgl_mulai'           => $tglMulaiJabatan,
                'status'              => 'aktif',
            ]);
        }

        // LOGIK RIWAYAT PENDIDIKAN AWAL
        $tglMulaiPendidikan = $request->tanggal_mulai_pendidikan ?? date('Y-m-d');
        if ($request->pendidikan_id) {
            RiwayatPendidikan::create([
                'pengurus_id'     => $pengurus->id,
                'pendidikan_id'   => $request->pendidikan_id,
                'tanggal_mulai'   => $tglMulaiPendidikan,
                'status'          => 'aktif',
            ]);
        }

        // LOGIK TUGAS MULTI-SELECT & SINGLE-SELECT
        $tugasArray = $request->input('tugas', []);
        
        if ($request->filled('tugas_internal_id')) {
            $tugasArray[] = [
                'id' => $request->tugas_internal_id, 
                'status' => 'aktif',
                'tgl_mulai' => $request->tgl_mulai_tugas_internal ?? date('Y-m-d')
            ];
        }
        if ($request->filled('tugas_eksternal_id')) {
            $tugasArray[] = [
                'id' => $request->tugas_eksternal_id, 
                'status' => 'aktif',
                'tgl_mulai' => $request->tgl_mulai_tugas_eksternal ?? date('Y-m-d')
            ];
        }

        if (!empty($tugasArray)) {
            foreach ($tugasArray as $item) {
                if (isset($item['id'])) {
                    $tugasId = $item['id'];
                    $status  = $item['status'];
                    $tglTugas = $item['tgl_mulai'] ?? date('Y-m-d'); // Tanggal independen tiap tugas

                    // B. Simpan ke Riwayat Tugas
                    RiwayatTugas::create([
                        'pengurus_id'     => $pengurus->id,
                        'tugas_id'        => $tugasId,
                        'tgl_mulai'       => $tglTugas,
                        'status'          => $status,
                    ]);
                }
            }
        }

        return redirect()->route('pokok.pengurus.index')
            ->with('success', 'Data pengurus berhasil ditambahkan!');
    }

    public function show($id)
    {
        $pengurus = Pengurus::with([
            'kamar.daerah.wilayah',
            'strukturJabatan',
            'tugas',
            'pendidikan',
            'riwayatJabatans.strukturJabatan',
            'riwayatTugas.masterTugas'
        ])->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isWilayah() && $pengurus->kamar?->daerah?->wilayah?->nama_wilayah != $user->wilayah) abort(403);
        if ($user->isDaerah() && $pengurus->kamar?->daerah?->nama_daerah != $user->daerah) abort(403);

        return view('pokok.pengurus.show', compact('pengurus'));
    }

    public function edit($id)
    {
        $pengurus = Pengurus::with(['kamar.daerah.wilayah', 'tugas', 'riwayatJabatans', 'riwayatTugas'])->findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isWilayah() && $pengurus->kamar?->daerah?->wilayah?->nama_wilayah != $user->wilayah) abort(403);
        if ($user->isDaerah() && $pengurus->kamar?->daerah?->nama_daerah != $user->daerah) abort(403);

        return view('pokok.pengurus.edit', [
            'pengurus'          => $pengurus,
            'wilayahs'          => Wilayah::select('nama_wilayah')->distinct()->orderBy('nama_wilayah')->pluck('nama_wilayah'),
            'daerahs'           => Daerah::whereHas('wilayah', function($q) use ($pengurus) { $q->where('nama_wilayah', $pengurus->kamar?->daerah?->wilayah?->nama_wilayah); })->select('nama_daerah')->distinct()->orderBy('nama_daerah')->pluck('nama_daerah'),
            'kamars'            => Kamar::whereHas('daerah', function($q) use ($pengurus) { $q->where('nama_daerah', $pengurus->kamar?->daerah?->nama_daerah); })->orderBy('nomor_kamar')->get(),
            'entitasList'       => MasterStrukturJabatan::select('entitas')->distinct()->orderBy('entitas')->get(),
            'jabatans'          => MasterStrukturJabatan::where('entitas', $pengurus->strukturJabatan?->entitas)->select('jabatan')->distinct()->get(),
            'jenis_jabatans'    => MasterStrukturJabatan::where('entitas', $pengurus->strukturJabatan?->entitas)->where('jabatan', $pengurus->strukturJabatan?->jabatan)->select('jenis_jabatan')->distinct()->get(),
            'grade_jabatans'    => MasterStrukturJabatan::where('entitas', $pengurus->strukturJabatan?->entitas)->where('jabatan', $pengurus->strukturJabatan?->jabatan)->where('jenis_jabatan', $pengurus->strukturJabatan?->jenis_jabatan)->get(),
            'fungsionalTugas'   => MasterTugas::where('jenis_tugas', 'fungsional')->orderBy('nama_tugas')->get(),
            'rangkapInternals'  => MasterTugas::where('jenis_tugas', 'internal')->orderBy('nama_tugas')->get(),
            'rangkapEksternals' => MasterTugas::where('jenis_tugas', 'eksternal')->orderBy('nama_tugas')->get(),
            'pendidikans'       => Pendidikan::orderBy('nama_pendidikan')->get(),
            'entitasDaerahs'    => \App\Models\Daerah::whereNotNull('entitas_daerah')->select('entitas_daerah')->distinct()->orderBy('entitas_daerah')->pluck('entitas_daerah'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $pengurus = Pengurus::with('kamar.daerah.wilayah')->findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isWilayah() && $pengurus->kamar?->daerah?->wilayah?->nama_wilayah != $user->wilayah) abort(403);
        if ($user->isDaerah() && $pengurus->kamar?->daerah?->nama_daerah != $user->daerah) abort(403);

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
        $pengurus->kamar_id             = $request->domisili_id ?? $pengurus->kamar_id;
        $pengurus->entitas_daerah       = $request->entitas_daerah;
        $pengurus->jabatan_id           = $request->jabatan_id;
        $pengurus->pendidikan_id        = $request->pendidikan_id;
        $pengurus->sk_kepengurusan      = $request->sk_kepengurusan;

        // Ambil status jika ada di request (dari form edit), jika tidak pakai status lama
        if ($request->has('status')) {
            $pengurus->status = $request->status;
        }

        $tglMulaiJabatan = $request->tgl_mulai_jabatan ?? date('Y-m-d');
        
        $activeRiwayatJabatan = RiwayatJabatan::where('pengurus_id', $pengurus->id)
                ->where('status', 'aktif')
                ->first();

        // Jika jabatan diubah, atau di non-aktifkan
        if ($pengurus->isDirty('struktur_jabatan_id') || ($pengurus->isDirty('status') && $pengurus->status == 'non_aktif')) {
            // Tutup jabatan lama yang masih aktif
            if ($activeRiwayatJabatan) {
                $activeRiwayatJabatan->update([
                    'tgl_selesai' => date('Y-m-d'),
                    'status' => 'non_aktif'
                ]);
            }
                
            // Jika pengurus aktif dan jabatan berubah, buat riwayat baru
            if ($pengurus->status == 'aktif') {
                RiwayatJabatan::create([
                    'pengurus_id' => $pengurus->id,
                    'jabatan_id' => $request->jabatan_id,
                    'tgl_mulai' => $tglMulaiJabatan,
                    'status' => 'aktif'
                ]);
            }
        } else {
            // Jika jabatan tidak berubah, tapi ini data lama (belum punya riwayat aktif), buatkan baru!
            if (!$activeRiwayatJabatan && $pengurus->status == 'aktif') {
                RiwayatJabatan::create([
                    'pengurus_id' => $pengurus->id,
                    'jabatan_id' => $pengurus->jabatan_id,
                    'tgl_mulai' => $tglMulaiJabatan,
                    'status' => 'aktif'
                ]);
            } elseif ($activeRiwayatJabatan && $activeRiwayatJabatan->tgl_mulai != $tglMulaiJabatan) {
                // Jika jabatan tidak berubah, tapi admin mengedit tanggal mulainya dari form
                $activeRiwayatJabatan->update(['tgl_mulai' => $tglMulaiJabatan]);
            }
        }

        // LOGIK RIWAYAT PENDIDIKAN UPDATE
        $tglMulaiPendidikan = $request->tanggal_mulai_pendidikan ?? date('Y-m-d');
        $activeRiwayatPendidikan = RiwayatPendidikan::where('pengurus_id', $pengurus->id)
                ->where('status', 'aktif')
                ->first();

        // Jika pendidikan diubah, atau di non-aktifkan
        if ($pengurus->isDirty('pendidikan_id') || ($pengurus->isDirty('status') && $pengurus->status == 'non_aktif')) {
            // Tutup pendidikan lama yang masih aktif
            if ($activeRiwayatPendidikan) {
                $activeRiwayatPendidikan->update([
                    'tanggal_selesai' => date('Y-m-d'),
                    'status' => 'non_aktif'
                ]);
            }
                
            // Jika pengurus aktif dan pendidikan berubah, buat riwayat baru
            if ($pengurus->status == 'aktif' && $request->pendidikan_id) {
                RiwayatPendidikan::create([
                    'pengurus_id' => $pengurus->id,
                    'pendidikan_id' => $request->pendidikan_id,
                    'tanggal_mulai' => $tglMulaiPendidikan,
                    'status' => 'aktif'
                ]);
            }
        } else {
            // Jika pendidikan tidak berubah, tapi ini data lama (belum punya riwayat aktif), buatkan baru!
            if (!$activeRiwayatPendidikan && $pengurus->status == 'aktif' && $pengurus->pendidikan_id) {
                RiwayatPendidikan::create([
                    'pengurus_id' => $pengurus->id,
                    'pendidikan_id' => $pengurus->pendidikan_id,
                    'tanggal_mulai' => $tglMulaiPendidikan,
                    'status' => 'aktif'
                ]);
            } elseif ($activeRiwayatPendidikan && $activeRiwayatPendidikan->tanggal_mulai != $tglMulaiPendidikan) {
                // Jika pendidikan tidak berubah, tapi admin mengedit tanggal mulainya dari form
                $activeRiwayatPendidikan->update(['tanggal_mulai' => $tglMulaiPendidikan]);
            }
        }

        if ($user->isAdmin() || $user->isBiktren() || $user->isWilayah()) {
            if ($request->filled('domisili_id')) {
                $pengurus->kamar_id = $request->domisili_id;
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
        $tugasArray = $request->input('tugas', []);
        
        // Tugas Internal & Eksternal
        if ($request->filled('tugas_internal_id')) {
            $tugasArray[] = [
                'id' => $request->tugas_internal_id, 
                'status' => 'aktif',
                'tgl_mulai' => $request->tgl_mulai_tugas_internal ?? date('Y-m-d')
            ];
        }
        if ($request->filled('tugas_eksternal_id')) {
            $tugasArray[] = [
                'id' => $request->tugas_eksternal_id, 
                'status' => 'aktif',
                'tgl_mulai' => $request->tgl_mulai_tugas_eksternal ?? date('Y-m-d')
            ];
        }
        $inputTugas = collect($tugasArray);
        $inputTugasIds = $inputTugas->pluck('id')->toArray();

        // A. Kelola Riwayat Tugas
        $activeRiwayatTugas = RiwayatTugas::where('pengurus_id', $pengurus->id)
                                ->where('status', 'aktif')
                                ->get();
                                
        // 1. Cek mana yang dicabut centangnya (atau jika pengurus non-aktif)
        foreach ($activeRiwayatTugas as $riwayat) {
            if ($pengurus->status == 'non_aktif' || !in_array($riwayat->tugas_id, $inputTugasIds)) {
                $riwayat->update([
                    'tgl_selesai' => date('Y-m-d'),
                    'status' => 'non_aktif'
                ]);
            }
        }

        // 2. Cek mana yang baru ditambah atau update tanggal mulainya
        if ($pengurus->status == 'aktif') {
            foreach ($inputTugas as $item) {
                $existingActive = $activeRiwayatTugas->firstWhere('tugas_id', $item['id']);
                $tglTugas = $item['tgl_mulai'] ?? date('Y-m-d');
                
                if (!$existingActive) {
                    RiwayatTugas::create([
                        'pengurus_id' => $pengurus->id,
                        'tugas_id' => $item['id'],
                        'tgl_mulai' => $tglTugas,
                        'status' => 'aktif'
                    ]);
                } elseif ($existingActive->tgl_mulai != $tglTugas) {
                    // Update tanggal mulainya jika ada perubahan di form
                    $existingActive->update(['tgl_mulai' => $tglTugas]);
                }
            }
        }

        return redirect()->route('pokok.pengurus.index')
            ->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengurus = Pengurus::with('kamar.daerah.wilayah')->findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isDaerah()) abort(403);
        if ($user->isWilayah() && $pengurus->kamar?->daerah?->wilayah?->nama_wilayah != $user->wilayah) abort(403);

        $fileFields = ['foto', 'berkas_sk_pengurus', 'berkas_surat_tugas', 'berkas_plt', 'berkas_lain'];
        foreach ($fileFields as $field) {
            if ($pengurus->{$field} && Storage::disk('public')->exists($pengurus->{$field})) {
                // Jangan hapus fisik file karena kita menggunakan Soft Deletes.
                // Arsip butuh foto dan berkas.
                // Storage::disk('public')->delete($pengurus->{$field}); 
            }
        }

        // Set status pengurus menjadi non_aktif
        $pengurus->update(['status' => 'non_aktif']);

        // Tutup semua riwayat yang masih aktif
        RiwayatJabatan::where('pengurus_id', $pengurus->id)->where('status', 'aktif')->update([
            'status' => 'non_aktif',
            'tgl_selesai' => date('Y-m-d')
        ]);
        RiwayatTugas::where('pengurus_id', $pengurus->id)->where('status', 'aktif')->update([
            'status' => 'non_aktif',
            'tgl_selesai' => date('Y-m-d')
        ]);
        RiwayatPendidikan::where('pengurus_id', $pengurus->id)->where('status', 'aktif')->update([
            'status' => 'non_aktif',
            'tanggal_selesai' => date('Y-m-d')
        ]);

        $pengurus->delete();

        return redirect()->route('pokok.pengurus.index')
            ->with('success', 'Data pengurus berhasil dihapus.');
    }

    public function export(Request $request)
    {
        return Excel::download(new PengurusExport($request), 'pengurus.xlsx');
    }

    public function import(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Hanya admin yang dapat mengimport data.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new PengurusImport();
            Excel::import($import, $request->file('file'));
            
            $msg = 'Selesai memproses import. ';
            if ($import->successRows > 0) $msg .= $import->successRows . ' data berhasil masuk. ';
            if ($import->failedRows > 0) $msg .= $import->failedRows . ' data dilewati karena kamar/jabatan tidak ada. ';
            
            $failures = $import->failures();
            if (count($failures) > 0) {
                $msg .= count($failures) . ' baris gagal (contoh: NIUP ganda/kosong). ';
                return redirect()->back()->with('warning', $msg);
            }
            
            return redirect()->back()->with($import->failedRows > 0 ? 'warning' : 'success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $filePath = public_path('template_import_pengurus.xlsx');
        
        // Buat template jika belum ada secara fisik
        if (!file_exists($filePath)) {
            // Karena butuh file fisik, kita bisa pakai PhpSpreadsheet untuk men-generate
            // atau menggunakan Excel::download dengan array export.
            $export = new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function array(): array {
                    return [
                        ['1122334455', 'Ahmad Budi', 'Kamar A1', 'Pusat', 'Ketua Daerah', 'SK-123', 'S1 Teknik', '2023-01-01']
                    ];
                }
                public function headings(): array {
                    return ['NIUP', 'Nama Lengkap', 'Nama Kamar', 'Entitas Daerah', 'Nama Jabatan', 'SK Kepengurusan', 'Pendidikan Terakhir', 'Tanggal Mulai Tugas'];
                }
            };
            return Excel::download($export, 'template_import_pengurus.xlsx');
        }

        return response()->download($filePath);
    }
}
