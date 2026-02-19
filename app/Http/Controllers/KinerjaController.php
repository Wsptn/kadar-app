<?php

namespace App\Http\Controllers;

use App\Models\Kinerja;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KinerjaController extends Controller
{
    // 1. HALAMAN RIWAYAT (INDEX)
    public function index(\Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $query = \App\Models\Pengurus::with(['kinerja', 'jabatan', 'wilayah', 'daerah']);

        if ($user->isAdmin() || $user->isBiktren()) {
        } elseif ($user->isWilayah()) {
            $query->where('wilayah_id', $user->wilayah_id);
        } elseif ($user->isDaerah()) {
            $query->where('daerah_id', $user->daerah_id);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status_penilaian')) {
            if ($request->status_penilaian == 'sudah') {
                $query->whereHas('kinerja');
            } elseif ($request->status_penilaian == 'belum') {
                $query->doesntHave('kinerja');
            }
        }

        $pengurus = $query->paginate(12)->withQueryString();

        return view('pokok.kinerja.index', compact('pengurus'));
    }

    // 2. HALAMAN INPUT NILAI (CREATE)
    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $selected_id = $request->query('pengurus_id');

        if ($selected_id) {
            $target = Pengurus::find($selected_id);
            if ($target && ($target->id == $user->pengurus_id || $target->niup == $user->niup)) {
                return redirect()->route('pokok.kinerja.index')
                    ->with('error', 'Tidak bisa input nilai diri sendiri, penilaian harus sesuai dengan struktur pesantren.');
            }
        }

        $query = Pengurus::query();

        if ($user->isAdmin()) {
        } elseif ($user->isBiktren()) {
            $query->whereHas('jabatan', function ($q) {
                $q->where('nama_jabatan', 'like', '%Kepala Wilayah%');
            });
        } elseif ($user->isWilayah()) {
            $query->where('wilayah_id', $user->wilayah_id)
                ->where('id', '!=', $user->pengurus_id) // Tidak bisa menilai diri sendiri
                ->whereHas('jabatan', function ($q) {
                    $q->where('nama_jabatan', 'not like', '%Kepala Wilayah%');
                });
        } elseif ($user->isDaerah()) {
            $query->where('daerah_id', $user->daerah_id)
                ->where('id', '!=', $user->pengurus_id)
                ->whereHas('jabatan', function ($q) {
                    $q->where('nama_jabatan', 'like', '%Daerah%')
                        ->where('nama_jabatan', 'not like', '%Kepala Daerah%');
                });
        }

        $pengurus = $query->get();

        // Validasi Akses via URL
        if ($selected_id && !$pengurus->contains('id', $selected_id)) {
            return redirect()->route('pokok.kinerja.index')
                ->with('error', 'Akses Ditolak: Anda tidak memiliki wewenang untuk menilai pengurus ini.');
        }

        return view('pokok.kinerja.create', compact('pengurus', 'selected_id'));
    }

    // 3. PROSES SIMPAN NILAI
    public function store(Request $request)
    {
        $user = auth()->user();
        $targetPengurus = \App\Models\Pengurus::find($request->pengurus_id);

        if ($targetPengurus && $targetPengurus->niup == $user->niup) {
            return redirect()->route('pokok.kinerja.index')
                ->with('error', 'Gagal Menyimpan: Tidak bisa input nilai diri sendiri, tindakan mencurigakan terdeteksi!');
        }
        $request->validate([
            'pengurus_id' => 'required',
            'tanggal_penilaian' => 'required|date',
            'skor_disiplin_waktu' => 'required|numeric|min:0|max:100',
            'skor_tanggung_jawab_izin' => 'required|numeric|min:0|max:100',
            'skor_selesai_tugas' => 'required|numeric|min:0|max:100',
            'skor_loyalitas' => 'required|numeric|min:0|max:100',
            'skor_akhlak' => 'required|numeric|min:0|max:100',
            'skor_contoh' => 'required|numeric|min:0|max:100',
            'skor_tupoksi' => 'required|numeric|min:0|max:100',
            'skor_komunikasi' => 'required|numeric|min:0|max:100',
            'skor_koordinasi' => 'required|numeric|min:0|max:100',
            'skor_kebersamaan' => 'required|numeric|min:0|max:100',
        ]);

        // Hitung Bobot
        $total = ($request->skor_disiplin_waktu * 0.13) +
            ($request->skor_tanggung_jawab_izin * 0.11) +
            ($request->skor_selesai_tugas * 0.12) +
            ($request->skor_loyalitas * 0.08) +
            ($request->skor_akhlak * 0.14) +
            ($request->skor_contoh * 0.12) +
            ($request->skor_tupoksi * 0.11) +
            ($request->skor_komunikasi * 0.07) +
            ($request->skor_koordinasi * 0.07) +
            ($request->skor_kebersamaan * 0.05);

        // Tentukan Predikat
        if ($total >= 90) {
            $huruf = 'A';
            $rekomendasi = 'Apresiasi & kaderisasi';
        } elseif ($total >= 75) {
            $huruf = 'B';
            $rekomendasi = 'Bimbingan ringan';
        } elseif ($total >= 70) {
            $huruf = 'C';
            $rekomendasi = 'Pembinaan sedang';
        } elseif ($total >= 50) {
            $huruf = 'D';
            $rekomendasi = 'Pembinaan intensif';
        } else {
            $huruf = 'E';
            $rekomendasi = 'Penanganan khusus (Merujuk ke SOP)';
        }

        // Simpan ke Database
        Kinerja::create([
            'pengurus_id' => $request->pengurus_id,
            'tanggal_penilaian' => $request->tanggal_penilaian,

            // Skor
            'skor_disiplin_waktu' => $request->skor_disiplin_waktu,
            'skor_tanggung_jawab_izin' => $request->skor_tanggung_jawab_izin,
            'skor_selesai_tugas' => $request->skor_selesai_tugas,
            'skor_loyalitas' => $request->skor_loyalitas,
            'skor_akhlak' => $request->skor_akhlak,
            'skor_contoh' => $request->skor_contoh,
            'skor_tupoksi' => $request->skor_tupoksi,
            'skor_komunikasi' => $request->skor_komunikasi,
            'skor_koordinasi' => $request->skor_koordinasi,
            'skor_kebersamaan' => $request->skor_kebersamaan,

            // Hasil
            'nilai_total' => $total,
            'huruf_mutu' => $huruf,
            'rekomendasi' => $rekomendasi,
            'catatan' => $request->catatan,

            'status_tindak_lanjut' => 'belum',
            'tanggal_tindak_lanjut' => null,
        ]);

        return redirect()->route('pokok.kinerja.index')->with('success', 'Penilaian Berhasil Disimpan!');
    }

    // 4. HALAMAN RIWAYAT (DETAIL)
    public function show($id)
    {
        $pengurus = Pengurus::with(['kinerja' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        return view('pokok.kinerja.show', compact('pengurus'));
    }

    // 5. PROSES TANDAI SUDAH DITANGANI (PEMBINAAN OFFLINE)
    public function markAsHandled(Request $request, $id)
    {
        $request->validate(['deskripsi_tindak_lanjut' => 'required|string|min:5']);

        $user = auth()->user();
        $kinerja = Kinerja::with(['pengurus'])->findOrFail($id);
        $target = $kinerja->pengurus;

        $bolehUpdate = false;

        if ($user->isAdmin() || $user->isBiktren()) {
            $bolehUpdate = true;
        } elseif ($user->isWilayah()) {
            if ($target->entitas_id == 2 && $target->wilayah_id == $user->wilayah_id) {
                $bolehUpdate = true;
            } else {
                return redirect()->back()->with('error', 'Wewenang Wilayah hanya untuk level Daerah.');
            }
        }

        if ($bolehUpdate) {
            $kinerja->update([
                'status_tindak_lanjut' => 'sudah',
                'deskripsi_tindak_lanjut' => $request->deskripsi_tindak_lanjut,
                'tanggal_tindak_lanjut' => now(),
            ]);
            return redirect()->back()->with('success', 'Berhasil mencatat tindak lanjut.');
        }

        return redirect()->back()->with('error', 'Akses Ditolak.');
    }
}
