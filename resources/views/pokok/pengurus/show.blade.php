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
                {{-- Hapus 'h-100' agar tinggi otomatis (compact), ubah p-4 jadi p-3 --}}
                <div class="card shadow-sm p-3 text-center border-0">

                    <div class="d-flex flex-column align-items-center">
                        {{-- Foto --}}
                        @if ($pengurus->foto)
                            <img src="{{ asset('storage/' . $pengurus->foto) }}" class="rounded border p-1 mb-3"
                                style="width:180px; height:220px; object-fit:cover;"
                                onerror="this.src='{{ asset('template-admin/img/default-avatar.png') }}'">
                        @else
                            <img src="{{ asset('template-admin/img/default-avatar.png') }}" class="rounded border p-1 mb-3"
                                style="width:180px; height:220px; object-fit:cover;">
                        @endif

                        <h4 class="fw-bold mb-1">{{ $pengurus->nama }}</h4>
                        <p class="text-muted mb-3 small">NIUP: <strong>{{ $pengurus->niup }}</strong></p>

                        @php
                            $statusClass = $pengurus->status == 'aktif' ? 'bg-success' : 'bg-secondary';
                            $statusText = $pengurus->status == 'aktif' ? 'AKTIF' : 'NON-AKTIF';
                        @endphp

                        {{-- Status: Diberi width 180px agar sama persis dengan gambar --}}
                        <span class="badge {{ $statusClass }} py-2 rounded-pill"
                            style="width: 180px; display: inline-block; font-size: 0.9rem;">
                            {{ $statusText }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- DATA DETAIL --}}
            <div class="col-lg-8">
                <div class="card shadow-sm p-4 position-relative h-100">

                    {{-- Tombol X di pojok kanan atas --}}
                    <div class="position-absolute top-0 end-0 p-3">
                        <a href="{{ route('pokok.pengurus.index') }}"
                            class="btn btn-light btn-sm shadow-sm border text-secondary" data-bs-toggle="tooltip"
                            title="Kembali">
                            <i class="bi bi-x-lg fw-bold"></i> Tutup
                        </a>
                    </div>

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

                            {{-- !!! BAGIAN PENTING: FUNGSIONAL TUGAS (UPDATED TAMPILAN BADGE) !!! --}}
                            <div class="mb-2 d-flex">
                                <label class="fw-semibold text-muted" style="width: 200px;">Fungsional Tugas</label>
                                <div class="flex-grow-1 d-flex align-items-start">
                                    <span class="me-1">:</span>
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
                            {{-- ============================================================== --}}

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
                                            class="text-decoration-none btn btn-sm btn-outline-success py-0 px-2 ms-1">
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
