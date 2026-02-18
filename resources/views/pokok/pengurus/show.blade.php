@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">

        {{-- Breadcrumb --}}
        <div class="bg-light p-2 mb-4 border rounded small">
            <span>
                Data Pokok / Pengurus /
                <span class="text-success fw-semibold">Detail Pengurus</span>
            </span>
        </div>

        <div class="row">

            {{-- FOTO & IDENTITAS (Kiri) --}}
            <div class="col-lg-4 mb-4">
                {{-- Card Profil --}}
                <div class="card shadow-sm p-3 text-center border-0">

                    <div class="d-flex flex-column align-items-center">
                        {{-- 1. FOTO --}}
                        @if ($pengurus->foto)
                            <img src="{{ asset('storage/' . $pengurus->foto) }}" class="rounded border p-1 mb-3"
                                style="width:180px; height:220px; object-fit:cover;"
                                onerror="this.src='{{ asset('template-admin/img/default-avatar.png') }}'">
                        @else
                            <img src="{{ asset('template-admin/img/default-avatar.png') }}" class="rounded border p-1 mb-3"
                                style="width:180px; height:220px; object-fit:cover;">
                        @endif

                        {{-- 2. NAMA & NIUP --}}
                        <h4 class="fw-bold mb-1">{{ $pengurus->nama }}</h4>
                        <p class="text-muted mb-3 small">NIUP: <strong>{{ $pengurus->niup }}</strong></p>

                        {{-- 3. STATUS AKTIF --}}
                        @php
                            $statusClass = $pengurus->status == 'aktif' ? 'bg-success' : 'bg-secondary';
                            $statusText = $pengurus->status == 'aktif' ? 'AKTIF' : 'NON-AKTIF';

                            // Ambil Data Kinerja Terakhir
                            $lastKinerja = $pengurus->kinerja->last();
                        @endphp

                        <span class="badge {{ $statusClass }} py-2 rounded-pill mb-3"
                            style="width: 180px; display: inline-block; font-size: 0.9rem;">
                            {{ $statusText }}
                        </span>

                        {{-- 4. TAMBAHAN: STATUS PENILAIAN & REKOMENDASI --}}
                        @if ($lastKinerja)
                            <div class="card bg-light border-0 p-2" style="width: 180px;">
                                <small class="text-muted d-block fw-bold"
                                    style="font-size: 0.65rem; text-transform: uppercase;">
                                    Kinerja Terakhir
                                </small>

                                {{-- Angka Nilai --}}
                                <div
                                    class="display-6 fw-bold my-1 
                        {{ $lastKinerja->nilai_total >= 75 ? 'text-primary' : ($lastKinerja->nilai_total >= 60 ? 'text-warning' : 'text-danger') }}">
                                    {{ $lastKinerja->nilai_total }}
                                </div>

                                {{-- Badge Rekomendasi --}}
                                @if ($lastKinerja->rekomendasi == 'Kinerja Memuaskan')
                                    <span class="badge bg-success w-100 text-wrap lh-sm py-2">
                                        {{ $lastKinerja->rekomendasi }}
                                    </span>
                                @elseif($lastKinerja->rekomendasi == 'Pendampingan')
                                    <span class="badge bg-warning text-dark w-100 text-wrap lh-sm py-2">
                                        {{ $lastKinerja->rekomendasi }}
                                    </span>
                                @else
                                    <span class="badge bg-danger w-100 text-wrap lh-sm py-2">
                                        {{ $lastKinerja->rekomendasi }}
                                    </span>
                                @endif

                                {{-- Tanggal --}}
                                <small class="text-muted d-block mt-2" style="font-size: 0.6rem">
                                    {{ \Carbon\Carbon::parse($lastKinerja->tanggal_penilaian)->format('d M Y') }}
                                </small>
                            </div>
                        @else
                            {{-- Jika Belum Ada Nilai --}}
                            <div class="card bg-light border-0 p-2 d-flex align-items-center justify-content-center"
                                style="width: 180px; height: 100px; border-style: dashed !important;">
                                <span class="text-muted small">Belum ada<br>Data Penilaian</span>
                            </div>
                        @endif

                    </div>

                </div>
            </div>

            {{-- DATA DETAIL (Kanan) --}}
            <div class="col-lg-8">
                <div class="card shadow-sm p-4 position-relative h-100">

                    {{-- Tombol X (Tutup) --}}
                    <div class="position-absolute top-0 end-0 p-3">
                        <a href="{{ route('pokok.pengurus.index') }}"
                            class="btn btn-light btn-sm shadow-sm border text-secondary" data-bs-toggle="tooltip"
                            title="Kembali">
                            <i class="bi bi-x-lg fw-bold"></i> Tutup
                        </a>
                    </div>

                    <h5 class="fw-bold text-success border-bottom pb-2 mb-3">Informasi Detail</h5>

                    {{-- BAGIAN 1: LOKASI --}}
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Wilayah</div>
                        <div class="col-sm-8 fw-bold text-dark">: {{ $pengurus->wilayah->nama_wilayah ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Daerah</div>
                        <div class="col-sm-8 fw-bold text-dark">: {{ $pengurus->daerah->nama_daerah ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Entitas Daerah</div>
                        <div class="col-sm-8">: {{ $pengurus->entitas_daerah ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Kamar</div>
                        <div class="col-sm-8">: {{ $pengurus->kamar->nomor_kamar ?? '-' }}</div>
                    </div>

                    <hr class="my-3 text-muted opacity-50">

                    {{-- BAGIAN 2: KELEMBAGAAN & TUGAS --}}
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Entitas</div>
                        <div class="col-sm-8">: {{ $pengurus->entitas->nama_entitas ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Jabatan</div>
                        <div class="col-sm-8">: {{ $pengurus->jabatan->nama_jabatan ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Jenis & Grade</div>
                        <div class="col-sm-8">
                            : {{ $pengurus->jenisJabatan->jenis_jabatan ?? '-' }} /
                            {{ $pengurus->gradeJabatan->grade ?? '-' }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">SK Kepengurusan</div>
                        <div class="col-sm-8">: {{ $pengurus->sk_kepengurusan ?? '-' }}</div>
                    </div>

                    {{-- Fungsional Tugas --}}
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Fungsional Tugas</div>
                        <div class="col-sm-8 d-flex">
                            <span class="me-1">:</span>
                            <div>
                                @if ($pengurus->fungsionalTugas->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($pengurus->fungsionalTugas as $ft)
                                            <span
                                                class="badge {{ $ft->pivot->status == 'aktif' ? 'bg-primary' : 'bg-secondary' }} bg-opacity-75">
                                                {{ $ft->tugas }}
                                                @if ($ft->pivot->status != 'aktif')
                                                    (Non-Aktif)
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Tidak ada tugas fungsional</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Rangkap Internal</div>
                        <div class="col-sm-8">: {{ $pengurus->rangkapInternal->internal ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Rangkap Eksternal</div>
                        <div class="col-sm-8">: {{ $pengurus->rangkapEksternal->eksternal ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Pendidikan</div>
                        <div class="col-sm-8">: {{ $pengurus->pendidikan->nama_pendidikan ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Angkatan</div>
                        <div class="col-sm-8">: {{ $pengurus->angkatan->angkatan ?? '-' }}</div>
                    </div>

                    <hr class="my-3 text-muted opacity-50">
                    <h6 class="fw-bold text-success mb-3">Berkas Lampiran</h6>

                    {{-- FILE LOOPING --}}
                    @php
                        $files = [
                            'Berkas SK Pengurus' => $pengurus->berkas_sk_pengurus,
                            'Berkas Surat Tugas' => $pengurus->berkas_surat_tugas,
                            'Berkas PLT' => $pengurus->berkas_plt,
                            'Berkas Lain' => $pengurus->berkas_lain,
                        ];
                    @endphp

                    @foreach ($files as $label => $path)
                        <div class="row mb-2">
                            <div class="col-sm-4 fw-semibold text-muted">{{ $label }}</div>
                            <div class="col-sm-8">
                                :
                                @if ($path)
                                    <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                        class="text-decoration-none btn btn-sm btn-outline-success py-0 px-2 ms-1">
                                        <i class="bi bi-file-earmark-text me-1"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic ms-1">Tidak ada file</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- BUTTON EDIT + HAPUS --}}
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('pokok.pengurus.edit', $pengurus->id) }}"
                            class="btn btn-warning me-2 px-4 text-white fw-bold">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>

                        @if (!Auth::user()->isDaerah())
                            <form method="POST" action="{{ route('pokok.pengurus.destroy', $pengurus->id) }}"
                                onsubmit="return confirm('Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger px-4 fw-bold">
                                    <i class="bi bi-trash me-1"></i> Hapus
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
