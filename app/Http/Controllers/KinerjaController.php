<?php

namespace App\Http\Controllers;

use App\Models\Kinerja;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KinerjaController extends Controller
{
    // 1. HALAMAN UTAMA (GRID KARTU)
    public function index(Request $request)
    {
        // Ambil pengurus aktif beserta riwayat kinerjanya
        $query = Pengurus::with(['kinerja'])
            ->where('status', 'aktif');

        // Filter Pencarian Nama
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Filter Status (Sudah/Belum Dinilai)
        if ($request->filled('status_penilaian')) {
            if ($request->status_penilaian == 'sudah') {
                $query->whereHas('kinerja');
            } elseif ($request->status_penilaian == 'belum') {
                $query->whereDoesntHave('kinerja');
            }
        }

        $pengurus = $query->paginate(12)->withQueryString();

        return view('pokok.kinerja.index', compact('pengurus'));
    }

    // 2. HALAMAN INPUT NILAI
    public function create(Request $request)
    {
        $pengurus = Pengurus::where('status', 'aktif')->get();
        $selected_id = $request->query('pengurus_id');
        return view('pokok.kinerja.create', compact('pengurus', 'selected_id'));
    }

    // 3. PROSES SIMPAN NILAI
    public function store(Request $request)
    {
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
        $huruf = 'E';
        $rekomendasi = 'Pembinaan';
        if ($total >= 90) {
            $huruf = 'A';
            $rekomendasi = 'Kinerja Memuaskan';
        } elseif ($total >= 75) {
            $huruf = 'B';
            $rekomendasi = 'Kinerja Memuaskan';
        } elseif ($total >= 60) {
            $huruf = 'C';
            $rekomendasi = 'Pendampingan';
        } elseif ($total >= 50) {
            $huruf = 'D';
            $rekomendasi = 'Pembinaan';
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
        // Ambil pengurus + riwayat kinerja (urut dari terbaru)
        $pengurus = Pengurus::with(['kinerja' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        return view('pokok.kinerja.show', compact('pengurus'));
    }

    // 5. PROSES TANDAI SUDAH DITANGANI (PEMBINAAN OFFLINE)
    public function markAsHandled($id)
    {
        $kinerja = Kinerja::findOrFail($id);

        $kinerja->update([
            'status_tindak_lanjut' => 'sudah',
            'tanggal_tindak_lanjut' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Status berhasil diubah menjadi SUDAH DITANGANI.');
    }
}
