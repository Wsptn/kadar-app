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

class PengurusController extends Controller
{
    /**
     * Menampilkan Daftar Pengurus
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Pengurus::with(['wilayah', 'daerah', 'kamar', 'entitas', 'jabatan', 'fungsionalTugas']);

        // === 1. FILTER PERAN (User Wilayah/Daerah terkunci datanya) ===
        if ($user->isWilayah()) {
            $query->where('wilayah_id', $user->wilayah_id);
        } elseif ($user->isDaerah()) {
            $query->where('daerah_id', $user->daerah_id);
        }

        // === 2. FILTER PERMINTAAN (Dari Form Filter) ===

        // a. Wilayah (Hanya Admin/Biktren yang bisa filter ini)
        if ($request->filled('wilayah') && ($user->isAdmin() || $user->isBiktren())) {
            $query->where('wilayah_id', $request->wilayah);
        }

        // b. Daerah
        if ($request->filled('daerah')) {
            $query->where('daerah_id', $request->daerah);
        }


        // c. Entitas Daerah (Manual List)
        if ($request->filled('entitas_daerah')) {
            $query->where('entitas_daerah', $request->entitas_daerah);
        }

        // d. Entitas (Kelembagaan)
        if ($request->filled('entitas')) {
            $query->where('entitas_id', $request->entitas);
        }

        // e. Jabatan
        if ($request->filled('jabatan')) {
            $query->where('jabatan_id', $request->jabatan);
        }

        // ---------------------------

        // f. Fungsional Tugas
        if ($request->filled('tugas')) {
            $query->where('fungsional_tugas_id', $request->tugas);
        }

        // g. Tugas Internal
        if ($request->filled('internal')) {
            $query->where('rangkap_internal_id', $request->internal);
        }

        // h. Tugas Eksternal
        if ($request->filled('eksternal')) {
            $query->where('rangkap_eksternal_id', $request->eksternal);
        }

        // i. Pendidikan
        if ($request->filled('pendidikan')) {
            $query->where('pendidikan_id', $request->pendidikan);
        }

        // j. Angkatan
        if ($request->filled('angkatan')) {
            $query->where('angkatan_id', $request->angkatan);
        }

        // k. Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // l. Pencarian (Nama / NIUP)
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('nama', 'like', "%{$q}%")
                    ->orWhere('niup', 'like', "%{$q}%");
            });
        }

        // Ambil Data Utama
        $pengurus = $query->orderBy('nama', 'ASC')
            ->paginate(12)
            ->appends($request->all());

        // === 3. DATA MASTER UNTUK DROPDOWN ===

        // Wilayah & Daerah
        $wilayahList = Wilayah::orderBy('nama_wilayah')->get();
        $daerahList  = $request->filled('wilayah')
            ? Daerah::where('wilayah_id', $request->wilayah)->orderBy('nama_daerah')->get()
            : Daerah::orderBy('nama_daerah')->get();

        // Data Master Lainnya
        $fungsionalList = MasterFungsionalTugas::orderBy('tugas')->get();
        $internalList   = MasterTugasInternal::orderBy('internal')->get();
        $eksternalList  = MasterTugasEksternal::orderBy('eksternal')->get();
        $pendidikanList = Pendidikan::orderBy('nama_pendidikan')->get();
        $angkatanList   = Angkatan::orderBy('angkatan')->get();

        // --- TAMBAHAN DATA MASTER UNTUK VIEW ---
        $entitasList      = Entitas::orderBy('nama_entitas')->get();
        $jabatanList      = Jabatan::orderBy('nama_jabatan')->get();
        $jenisJabatanList = JenisJabatan::orderBy('jenis_jabatan')->get();
        $gradeJabatanList = GradeJabatan::orderBy('grade')->get();

        return view('pokok.pengurus.index', [
            'pengurus'         => $pengurus,
            'wilayahList'      => $wilayahList,
            'daerahList'       => $daerahList,
            'fungsionalList'   => $fungsionalList,
            'internalList'     => $internalList,
            'eksternalList'    => $eksternalList,
            'pendidikanList'   => $pendidikanList,
            'angkatanList'     => $angkatanList,
            // Kirim data baru ke view
            'entitasList'      => $entitasList,
            'jabatanList'      => $jabatanList,
            'jenisJabatanList' => $jenisJabatanList,
            'gradeJabatanList' => $gradeJabatanList,
        ]);
    }
    /**
     * Form Tambah Data
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // PROTEKSI: Akun Daerah DILARANG akses halaman ini
        if ($user->isDaerah()) {
            abort(403, 'Maaf, akun Daerah tidak diizinkan menambah data pengurus.');
        }

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

    /**
     * Proses Simpan Data
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. CEGAH DAERAH (Proteksi Ganda)
        if ($user->isDaerah()) {
            abort(403, 'Akses ditolak.');
        }

        // 2. VALIDASI INPUT (SERVER SIDE)
        $request->validate([
            // Identitas
            'niup'                 => 'required|string|max:50|unique:penguruses,niup',
            'nama'                 => 'required|string|max:255',

            // Lokasi (WAJIB SEMUA, TERMASUK KAMAR & ENTITAS DAERAH)
            'wilayah_id'           => 'required',
            'daerah_id'            => 'required',
            'entitas_daerah'       => 'nullable|string',
            'kamar_id'             => 'required|exists:kamars,id',

            // Kelembagaan (WAJIB SEMUA)
            'entitas_id'           => 'required|exists:entitas,id',
            'jabatan_id'           => 'required|exists:jabatans,id',
            'jenis_jabatan_id'     => 'required|exists:jenis_jabatans,id',
            'grade_jabatan_id'     => 'required|exists:grade_jabatans,id',

            // Data Pendukung (Boleh kosong/nullable)
            'sk_kepengurusan'      => 'nullable|string|max:255',
            'fungsional_tugas_id'  => 'nullable|exists:master_fungsional_tugas,id_tugas',
            'rangkap_internal_id'  => 'nullable|exists:master_tugas_internals,id_internal',
            'rangkap_eksternal_id' => 'nullable|exists:master_tugas_eksternals,id_eksternal',
            'pendidikan_id'        => 'nullable|exists:pendidikans,id_pendidikan',
            'angkatan_id'          => 'nullable|exists:angkatans,id_angkatan',

            // Berkas
            'foto'                 => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'berkas_sk_pengurus'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_surat_tugas'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_plt'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'berkas_lain'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // 3. LOGIKA PENENTUAN LOKASI (WILAYAH/DAERAH)
        $wilayahId = $request->wilayah_id;
        $daerahId  = $request->daerah_id;

        // Jika user Wilayah, paksa ID wilayah sesuai akunnya
        if ($user->isWilayah()) {
            $wilayahId = $user->wilayah_id;
            $daerahId  = $request->daerah_id; // Daerah tetap dari inputan
        }

        // Validasi Manual: Pastikan Lokasi Terisi
        if (!$wilayahId || !$daerahId) {
            return back()
                ->withErrors(['daerah_id' => 'Wilayah dan Daerah wajib diisi.'])
                ->withInput();
        }

        // 4. LOGIKA UNGGAH BERKAS

        // A. Unggah Foto (Boleh: Admin, Biktren, DAN Wilayah)
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('uploads/pengurus/foto', 'public');
        }

        // B. Unggah Dokumen Lain (HANYA: Admin & Biktren)
        // User Wilayah akan selalu null untuk bagian ini
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

        // 5. SIMPAN KE DATABASE
        Pengurus::create([
            'niup'                 => $request->niup,
            'nama'                 => $request->nama,
            'wilayah_id'           => $wilayahId,
            'daerah_id'            => $daerahId,
            'entitas_daerah'       => $request->entitas_daerah, // Simpan Entitas Daerah Manual
            'kamar_id'             => $request->kamar_id,
            'entitas_id'           => $request->entitas_id,
            'jabatan_id'           => $request->jabatan_id,
            'jenis_jabatan_id'     => $request->jenis_jabatan_id,
            'grade_jabatan_id'     => $request->grade_jabatan_id,
            'sk_kepengurusan'      => $request->sk_kepengurusan,
            'fungsional_tugas_id'  => $request->fungsional_tugas_id,
            'rangkap_internal_id'  => $request->rangkap_internal_id,
            'rangkap_eksternal_id' => $request->rangkap_eksternal_id,
            'pendidikan_id'        => $request->pendidikan_id,
            'angkatan_id'          => $request->angkatan_id,
            'status'               => 'aktif',

            // Jalur Berkas
            'foto'                 => $fotoPath,
            'berkas_sk_pengurus'   => $skPath,
            'berkas_surat_tugas'   => $suratPath,
            'berkas_plt'           => $pltPath,
            'berkas_lain'          => $lainPath,
        ]);

        return redirect()->route('pokok.pengurus.index')
            ->with('success', 'Data pengurus berhasil ditambahkan!');
    }

    /**
     * Detail Data
     */
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

        // Proteksi Hak Akses Show
        if ($user->isWilayah() && $pengurus->wilayah_id != $user->wilayah_id) abort(403);
        if ($user->isDaerah() && $pengurus->daerah_id != $user->daerah_id) abort(403);

        return view('pokok.pengurus.show', compact('pengurus'));
    }

    /**
     * Form Edit Data
     */
    public function edit($id)
    {
        $pengurus = Pengurus::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Proteksi Hak Akses Edit
        if ($user->isWilayah() && $pengurus->wilayah_id != $user->wilayah_id) {
            abort(403, 'Anda tidak berhak mengedit data dari wilayah lain.');
        }
        if ($user->isDaerah() && $pengurus->daerah_id != $user->daerah_id) {
            abort(403, 'Anda tidak berhak mengedit data dari daerah lain.');
        }

        // Muat Data Dropdown (Logika pra-isi ditangani di View melalui JS/Controller)
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $daerahs  = Daerah::where('wilayah_id', $pengurus->wilayah_id)->orderBy('nama_daerah')->get();
        $kamars   = Kamar::where('daerah_id', $pengurus->daerah_id)->orderBy('id')->get();

        $entitas        = Entitas::orderBy('nama_entitas')->get();
        $jabatans       = Jabatan::where('entitas_id', $pengurus->entitas_id)->get();
        $jenis_jabatans = JenisJabatan::where('jabatan_id', $pengurus->jabatan_id)->get();
        $grade_jabatans = GradeJabatan::where('jenis_jabatan_id', $pengurus->jenis_jabatan_id)->get();

        $fungsionalTugas   = MasterFungsionalTugas::orderBy('tugas')->get();
        $rangkapInternals  = MasterTugasInternal::orderBy('internal')->get();
        $rangkapEksternals = MasterTugasEksternal::orderBy('eksternal')->get();
        $pendidikans       = Pendidikan::orderBy('nama_pendidikan')->get();
        $angkatans         = Angkatan::orderBy('angkatan')->get();

        return view('pokok.pengurus.edit', compact(
            'pengurus',
            'wilayahs',
            'daerahs',
            'kamars',
            'entitas',
            'jabatans',
            'jenis_jabatans',
            'grade_jabatans',
            'fungsionalTugas',
            'rangkapInternals',
            'rangkapEksternals',
            'pendidikans',
            'angkatans'
        ));
    }

    /**
     * Proses Perbarui Data
     */
    public function update(Request $request, $id)
    {
        $pengurus = Pengurus::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Proteksi Update
        if ($user->isWilayah() && $pengurus->wilayah_id != $user->wilayah_id) abort(403);
        if ($user->isDaerah() && $pengurus->daerah_id != $user->daerah_id) abort(403);

        $request->validate([
            'niup'           => 'required',
            'nama'           => 'required',
            'entitas_daerah' => 'nullable|string', // Validasi Entitas Daerah
            'status'         => 'required|in:aktif,non_aktif',
            // Tambahkan validasi lain sesuai kebutuhan (mirip store)
        ]);

        // Perbarui Data Utama
        $pengurus->niup                 = $request->niup;
        $pengurus->nama                 = $request->nama;
        $pengurus->kamar_id             = $request->kamar_id;
        $pengurus->entitas_daerah       = $request->entitas_daerah; // Update Entitas Daerah
        $pengurus->entitas_id           = $request->entitas_id;
        $pengurus->jabatan_id           = $request->jabatan_id;
        $pengurus->jenis_jabatan_id     = $request->jenis_jabatan_id;
        $pengurus->grade_jabatan_id     = $request->grade_jabatan_id;
        $pengurus->fungsional_tugas_id  = $request->fungsional_tugas_id;
        $pengurus->rangkap_internal_id  = $request->rangkap_internal_id;
        $pengurus->rangkap_eksternal_id = $request->rangkap_eksternal_id;
        $pengurus->pendidikan_id        = $request->pendidikan_id;
        $pengurus->angkatan_id          = $request->angkatan_id;
        $pengurus->sk_kepengurusan      = $request->sk_kepengurusan;
        $pengurus->status               = $request->status;

        // Logika Update Lokasi
        if ($user->isAdmin() || $user->isBiktren()) {
            $pengurus->wilayah_id = $request->wilayah_id;
            $pengurus->daerah_id  = $request->daerah_id;
        } elseif ($user->isWilayah()) {
            // User Wilayah bisa pindah daerah (dalam wilayah yg sama)
            $pengurus->daerah_id = $request->daerah_id;
        }

        // === 1. PERBARUI FOTO (Semua boleh) ===
        if ($request->hasFile('foto')) {
            if ($pengurus->foto && Storage::disk('public')->exists($pengurus->foto)) {
                Storage::disk('public')->delete($pengurus->foto);
            }
            $pengurus->foto = $request->file('foto')->store('uploads/pengurus/foto', 'public');
        }

        // === 2. PERBARUI DOKUMEN LAIN (HANYA Admin/Biktren) ===
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

        return redirect()->route('pokok.pengurus.index')
            ->with('success', 'Data pengurus berhasil diperbarui.');
    }

    /**
     * Hapus Data
     */
    public function destroy($id)
    {
        $pengurus = Pengurus::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // DAERAH TIDAK BISA HAPUS
        if ($user->isDaerah()) {
            abort(403, 'Anda tidak diizinkan menghapus data.');
        }

        // WILAYAH HANYA HAPUS MILIK SENDIRI
        if ($user->isWilayah() && $pengurus->wilayah_id != $user->wilayah_id) {
            abort(403);
        }

        // Hapus file fisik
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
        return Excel::download(
            new PengurusExport($request),
            'pengurus.xlsx'
        );
    }
}
