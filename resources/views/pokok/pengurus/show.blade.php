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

            {{-- FOTO & IDENTITAS --}}
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm p-4 text-center h-100">

                    {{-- Foto --}}
                    @if ($pengurus->foto)
                        <img src="{{ asset('storage/' . $pengurus->foto) }}" class="rounded mx-auto d-block mb-3 border p-1"
                            style="width:180px; height:220px; object-fit:cover;"
                            onerror="this.src='{{ asset('template-admin/img/default-avatar.png') }}'">
                    @else
                        <img src="{{ asset('template-admin/img/default-avatar.png') }}"
                            class="rounded mx-auto d-block mb-3 border p-1"
                            style="width:180px; height:220px; object-fit:cover;">
                    @endif

                    <h4 class="fw-bold mb-1">{{ $pengurus->nama }}</h4>
                    <p class="text-muted mb-2">NIUP: <strong>{{ $pengurus->niup }}</strong></p>

                    @php
                        $statusClass = $pengurus->status == 'aktif' ? 'bg-success' : 'bg-secondary';
                        $statusText = $pengurus->status == 'aktif' ? 'AKTIF' : 'NON-AKTIF';
                    @endphp

                    <div>
                        <span class="badge {{ $statusClass }} px-4 py-2 rounded-pill">
                            {{ $statusText }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- DATA DETAIL --}}
            <div class="col-lg-8">
                <div class="card shadow-sm p-4 position-relative h-100">

                    {{-- Tombol X di pojok kanan atas --}}
                    <a href="{{ route('pokok.pengurus.index') }}"
                        class="btn btn-light border position-absolute d-flex align-items-center justify-content-center"
                        style="top:15px; right:15px; border-radius:50%; width:35px; height:35px; padding:0; font-weight:bold;"
                        title="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </a>

                    <h5 class="fw-bold text-success border-bottom pb-2 mb-3">Informasi Detail</h5>

                    <div class="row">

                        {{-- ===== BAGIAN 1: LOKASI ===== --}}
                        <div class="col-12">
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Wilayah</label>
                                <div class="fw-bold text-dark flex-grow-1">: {{ $pengurus->wilayah->nama_wilayah ?? '-' }}
                                </div>
                            </div>
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Daerah</label>
                                <div class="fw-bold text-dark flex-grow-1">: {{ $pengurus->daerah->nama_daerah ?? '-' }}
                                </div>
                            </div>
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Entitas Daerah</label>
                                <div class="flex-grow-1">: {{ $pengurus->entitas_daerah ?? '-' }}</div>
                            </div>
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Kamar</label>
                                <div class="flex-grow-1">: {{ $pengurus->kamar->nomor_kamar ?? '-' }}</div>
                            </div>
                        </div>

                        <hr class="my-3 text-muted opacity-50">

                        {{-- ===== BAGIAN 2: KELEMBAGAAN & TUGAS ===== --}}
                        <div class="col-12">
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Entitas</label>
                                <div class="flex-grow-1">: {{ $pengurus->entitas->nama_entitas ?? '-' }}</div>
                            </div>
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Jabatan</label>
                                <div class="flex-grow-1">: {{ $pengurus->jabatan->nama_jabatan ?? '-' }}</div>
                            </div>
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Jenis & Grade</label>
                                <div class="flex-grow-1">:
                                    {{ $pengurus->jenisJabatan->jenis_jabatan ?? '-' }} /
                                    {{ $pengurus->gradeJabatan->grade ?? '-' }}
                                </div>
                            </div>
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">SK Kepengurusan</label>
                                <div class="flex-grow-1">: {{ $pengurus->sk_kepengurusan ?? '-' }}</div>
                            </div>

                            {{-- !!! BAGIAN PENTING: FUNGSIONAL TUGAS (MULTI) !!! --}}
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Fungsional Tugas</label>
                                <div class="flex-grow-1 d-flex align-items-start">
                                    <span class="me-1">:</span>
                                    @if ($pengurus->fungsionalTugas->count() > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($pengurus->fungsionalTugas as $ft)
                                                <li class="mb-1 d-flex align-items-center">
                                                    <i class="bi bi-check-circle-fill text-success me-2 small"></i>
                                                    <span class="fw-semibold me-2">{{ $ft->tugas }}</span>

                                                    {{-- Tampilkan Status di Pivot --}}
                                                    @if ($ft->pivot->status == 'aktif')
                                                        <span class="badge bg-primary"
                                                            style="font-size: 0.65rem;">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary"
                                                            style="font-size: 0.65rem;">Non-Aktif</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted fst-italic">Tidak ada tugas fungsional</span>
                                    @endif
                                </div>
                            </div>
                            {{-- =============================================== --}}

                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Rangkap Internal</label>
                                <div class="flex-grow-1">: {{ $pengurus->rangkapInternal->internal ?? '-' }}</div>
                            </div>
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Rangkap Eksternal</label>
                                <div class="flex-grow-1">: {{ $pengurus->rangkapEksternal->eksternal ?? '-' }}</div>
                            </div>
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Pendidikan</label>
                                <div class="flex-grow-1">: {{ $pengurus->pendidikan->nama_pendidikan ?? '-' }}</div>
                            </div>
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Angkatan</label>
                                <div class="flex-grow-1">: {{ $pengurus->angkatan->angkatan ?? '-' }}</div>
                            </div>
                        </div>

                        <hr class="my-3 text-muted opacity-50">
                        <h6 class="fw-bold text-success mb-3">Berkas Lampiran</h6>

                        {{-- FILE LOOPING HELPER --}}
                        @php
                            $files = [
                                'Berkas SK Pengurus' => $pengurus->berkas_sk_pengurus,
                                'Berkas Surat Tugas' => $pengurus->berkas_surat_tugas,
                                'Berkas PLT' => $pengurus->berkas_plt,
                                'Berkas Lain' => $pengurus->berkas_lain,
                            ];
                        @endphp

                        @foreach ($files as $label => $path)
                            <div class="col-12 mb-2 d-flex justify-content-between">
                                <label class="fw-semibold text-muted" style="width: 200px;">{{ $label }}</label>
                                <div class="flex-grow-1">
                                    :
                                    @if ($path)
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                            class="text-decoration-none btn btn-sm btn-outline-primary py-0 px-2 ms-1">
                                            <i class="bi bi-file-earmark-text me-1"></i> Lihat File
                                        </a>
                                    @else
                                        <span class="text-muted small fst-italic ms-1">Tidak ada file</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>

                    {{-- BUTTON EDIT + HAPUS --}}
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('pokok.pengurus.edit', $pengurus->id) }}"
                            class="btn btn-warning me-2 px-4 text-white fw-bold">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>

                        {{-- Hanya Admin/Wilayah yang boleh hapus --}}
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
