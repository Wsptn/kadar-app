<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Models\Daerah;
use App\Models\Entitas;
use App\Models\GradeJabatan;
use App\Models\Jabatan;
use App\Models\JenisJabatan;
use App\Models\Kamar;
use App\Models\MasterFungsionalTugas;
use App\Models\MasterTugasEksternal;
use App\Models\MasterTugasInternal;
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
        $query = Pengurus::with(['wilayah', 'daerah', 'kamar', 'entitas', 'jabatan', 'fungsionalTugas']);

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
        if ($request->filled('entitas_daerah')) $query->where('entitas_daerah', $request->entitas_daerah);
        if ($request->filled('entitas')) $query->where('entitas_id', $request->entitas);
        if ($request->filled('jabatan')) $query->where('jabatan_id', $request->jabatan);

        if ($request->filled('tugas')) {
            $query->whereHas('fungsionalTugas', function ($q) use ($request) {
                $q->where('master_fungsional_tugas_id', $request->tugas);
            });
        }

        if ($request->filled('internal')) $query->where('rangkap_internal_id', $request->internal);
        if ($request->filled('eksternal')) $query->where('rangkap_eksternal_id', $request->eksternal);
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

        $fungsionalList = MasterFungsionalTugas::orderBy('tugas')->get();
        $internalList   = MasterTugasInternal::orderBy('internal')->get();
        $eksternalList  = MasterTugasEksternal::orderBy('eksternal')->get();
        $pendidikanList = Pendidikan::orderBy('nama_pendidikan')->get();
        $angkatanList   = Angkatan::orderBy('angkatan')->get();
        $entitasList      = Entitas::orderBy('nama_entitas')->get();
        $jabatanList      = Jabatan::orderBy('nama_jabatan')->get();
        $jenisJabatanList = JenisJabatan::orderBy('jenis_jabatan')->get();
        $gradeJabatanList = GradeJabatan::orderBy('grade')->get();

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
            'jenisJabatanList',
            'gradeJabatanList'
        ));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isDaerah()) abort(403, 'Maaf, akun Daerah tidak diizinkan menambah data pengurus.');

        return view('pokok.pengurus.create', [
            'wilayahs'          => Wilayah::orderBy('nama_wilayah')->get(),
            'entitas'           => Entitas::orderBy('nama_entitas')->get(),
            'fungsionalTugas'   => MasterFungsionalTugas::orderBy('tugas')->get(),
            'rangkapInternals'  => MasterTugasInternal::orderBy('internal')->get(),
            'rangkapEksternals' => MasterTugasEksternal::orderBy('eksternal')->get(),
            'pendidikans'       => Pendidikan::orderBy('nama_pendidikan')->get(),
            'angkatans'         => Angkatan::orderBy('angkatan')->get(),
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
            'entitas_id'           => 'required|exists:entitas,id',
            'jabatan_id'           => 'required|exists:jabatans,id',
            'jenis_jabatan_id'     => 'required|exists:jenis_jabatans,id',
            'grade_jabatan_id'     => 'required|exists:grade_jabatans,id',
            // Status tidak divalidasi karena default 'aktif'
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
            'entitas_daerah'       => $request->entitas_daerah,
            'kamar_id'             => $request->kamar_id,
            'entitas_id'           => $request->entitas_id,
            'jabatan_id'           => $request->jabatan_id,
            'jenis_jabatan_id'     => $request->jenis_jabatan_id,
            'grade_jabatan_id'     => $request->grade_jabatan_id,
            'sk_kepengurusan'      => $request->sk_kepengurusan,
            'rangkap_internal_id'  => $request->rangkap_internal_id,
            'rangkap_eksternal_id' => $request->rangkap_eksternal_id,
            'pendidikan_id'        => $request->pendidikan_id,
            'angkatan_id'          => $request->angkatan_id,
            'status'               => 'aktif', // Default Aktif
            'foto'                 => $fotoPath,
            'berkas_sk_pengurus'   => $skPath,
            'berkas_surat_tugas'   => $suratPath,
            'berkas_plt'           => $pltPath,
            'berkas_lain'          => $lainPath,
        ]);

        // LOGIK TUGAS MULTI-SELECT
        if ($request->has('tugas')) {
            foreach ($request->tugas as $item) {
                if (isset($item['id'])) {
                    $tugasId = $item['id'];
                    $status  = $item['status'];

                    // A. Simpan ke Pivot
                    $pengurus->fungsionalTugas()->attach($tugasId, ['status' => $status]);

                    // B. Otomatisasi (Copy Nama & NIUP)
                    $masterTugas = MasterFungsionalTugas::where('id_tugas', $tugasId)->first();
                    if ($masterTugas) {
                        $namaTugas = strtolower($masterTugas->tugas);
                        $dataLengkap = [
                            'status' => 'aktif',
                            'nama'   => $pengurus->nama,
                            'niup'   => $pengurus->niup
                        ];

                        if (str_contains($namaTugas, "mu'allim") || str_contains($namaTugas, "muallim")) {
                            if ($status == 'aktif') Muallim::firstOrCreate(['pengurus_id' => $pengurus->id], $dataLengkap);
                        }
                        if (str_contains($namaTugas, "wali asuh")) {
                            if ($status == 'aktif') WaliAsuh::firstOrCreate(['pengurus_id' => $pengurus->id], $dataLengkap);
                        }
                        if (str_contains($namaTugas, "pengajar")) {
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
            'entitas',
            'jabatan',
            'jenisJabatan',
            'gradeJabatan',
            'fungsionalTugas',
            'rangkapInternal',
            'rangkapEksternal',
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
        $pengurus = Pengurus::with('fungsionalTugas')->findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isWilayah() && $pengurus->wilayah_id != $user->wilayah_id) abort(403);
        if ($user->isDaerah() && $pengurus->daerah_id != $user->daerah_id) abort(403);

        return view('pokok.pengurus.edit', [
            'pengurus'          => $pengurus,
            'wilayahs'          => Wilayah::orderBy('nama_wilayah')->get(),
            'daerahs'           => Daerah::where('wilayah_id', $pengurus->wilayah_id)->orderBy('nama_daerah')->get(),
            'kamars'            => Kamar::where('daerah_id', $pengurus->daerah_id)->orderBy('id')->get(),
            'entitas'           => Entitas::orderBy('nama_entitas')->get(),
            'jabatans'          => Jabatan::where('entitas_id', $pengurus->entitas_id)->get(),
            'jenis_jabatans'    => JenisJabatan::where('jabatan_id', $pengurus->jabatan_id)->get(),
            'grade_jabatans'    => GradeJabatan::where('jenis_jabatan_id', $pengurus->jenis_jabatan_id)->get(),
            'fungsionalTugas'   => MasterFungsionalTugas::orderBy('tugas')->get(),
            'rangkapInternals'  => MasterTugasInternal::orderBy('internal')->get(),
            'rangkapEksternals' => MasterTugasEksternal::orderBy('eksternal')->get(),
            'pendidikans'       => Pendidikan::orderBy('nama_pendidikan')->get(),
            'angkatans'         => Angkatan::orderBy('angkatan')->get(),
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
            // 'status' => 'required', // Status sudah dihandle di logic bawah
        ]);

        // 1. UPDATE DATA UTAMA
        $pengurus->niup                 = $request->niup;
        $pengurus->nama                 = $request->nama;
        $pengurus->kamar_id             = $request->kamar_id;
        $pengurus->entitas_daerah       = $request->entitas_daerah;
        $pengurus->entitas_id           = $request->entitas_id;
        $pengurus->jabatan_id           = $request->jabatan_id;
        $pengurus->jenis_jabatan_id     = $request->jenis_jabatan_id;
        $pengurus->grade_jabatan_id     = $request->grade_jabatan_id;
        $pengurus->rangkap_internal_id  = $request->rangkap_internal_id;
        $pengurus->rangkap_eksternal_id = $request->rangkap_eksternal_id;
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

        // ==========================================================
        // 2. LOGIKA SINKRONISASI TUGAS (RELASI)
        // ==========================================================

        // Bersihkan dulu semua relasi lama di pivot
        $pengurus->fungsionalTugas()->detach();

        // Ambil input tugas dari form
        $inputTugas = collect($request->input('tugas', []));
        $allMasterTugas = MasterFungsionalTugas::all();

        foreach ($allMasterTugas as $master) {
            $namaTugas = strtolower($master->tugas);

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
                $pengurus->fungsionalTugas()->attach($master->id_tugas, ['status' => $statusTugas]);
            }

            // ======================================================
            // 3. SINKRONISASI KE TABEL SPESIFIK (Muallim/WaliAsuh)
            // ======================================================
            $dataSync = [
                'status' => $statusTugas,
                'nama'   => $pengurus->nama,
                'niup'   => $pengurus->niup
            ];

            // --- MU'ALLIM ---
            if (str_contains($namaTugas, "mu'allim") || str_contains($namaTugas, "muallim")) {
                if ($isDipilih) {
                    Muallim::updateOrCreate(['pengurus_id' => $pengurus->id], $dataSync);
                } else {
                    Muallim::where('pengurus_id', $pengurus->id)->update(['status' => 'non_aktif']);
                }
            }

            // --- WALI ASUH ---
            if (str_contains($namaTugas, "wali asuh")) {
                if ($isDipilih) {
                    WaliAsuh::updateOrCreate(['pengurus_id' => $pengurus->id], $dataSync);
                } else {
                    WaliAsuh::where('pengurus_id', $pengurus->id)->update(['status' => 'non_aktif']);
                }
            }

            // --- PENGAJAR ---
            if (str_contains($namaTugas, "pengajar")) {
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
