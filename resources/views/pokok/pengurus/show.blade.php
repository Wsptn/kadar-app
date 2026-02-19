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
            <div class="col-lg-4 mb-6">
                {{-- Card Profil --}}
                <div class="card shadow-sm p-3 text-center border-0 h-100">
                    <div class="d-flex flex-column align-items-center h-100">

                        {{-- 1. FOTO (Klik untuk Lihat Tab Baru) --}}
                        @php
                            $pathFoto = $pengurus->foto
                                ? asset('storage/' . $pengurus->foto)
                                : asset('template-admin/img/default-avatar.png');
                        @endphp
                        <a href="{{ $pathFoto }}" target="_blank" title="Klik untuk memperbesar">
                            <img src="{{ $pathFoto }}" class="rounded border p-1 mb-3 shadow-sm"
                                style="width:180px; height:220px; object-fit:cover; transition: transform .2s;"
                                onmouseover="this.style.transform='scale(1.02)'"
                                onmouseout="this.style.transform='scale(1)'"
                                onerror="this.src='{{ asset('template-admin/img/default-avatar.png') }}'">
                        </a>

                        {{-- 2. NAMA & NIUP --}}
                        <h4 class="fw-bold mb-1">{{ $pengurus->nama }}</h4>
                        <p class="text-muted mb-3 small">NIUP: <strong>{{ $pengurus->niup }}</strong></p>

                        {{-- 3. STATUS AKTIF --}}
                        @php
                            $statusClass = $pengurus->status == 'aktif' ? 'bg-success' : 'bg-secondary';
                            $statusText = $pengurus->status == 'aktif' ? 'AKTIF' : 'NON-AKTIF';
                            $lastKinerja = $pengurus->kinerja->last();
                        @endphp

                        <span class="badge {{ $statusClass }} py-2 rounded-pill mb-3"
                            style="width: 180px; display: inline-block; font-size: 0.9rem;">
                            {{ $statusText }}
                        </span>

                        {{-- 4. KINERJA TERAKHIR --}}
                        @if ($lastKinerja)
                            <div class="card bg-light border-0 p-3 mb-2 w-100" style="max-width: 280px;">
                                <small class="text-muted d-block fw-bold mb-1"
                                    style="font-size: 0.65rem; text-transform: uppercase;">
                                    Hasil Kinerja Terakhir
                                </small>

                                <div
                                    class="display-6 fw-bold my-1 
                        {{ $lastKinerja->nilai_total >= 75 ? 'text-primary' : ($lastKinerja->nilai_total >= 70 ? 'text-warning' : 'text-danger') }}">
                                    {{ $lastKinerja->nilai_total }}
                                </div>

                                <span
                                    class="badge {{ $lastKinerja->nilai_total >= 75 ? 'bg-success' : ($lastKinerja->nilai_total >= 70 ? 'bg-warning text-dark' : 'bg-danger') }} mb-2 text-wrap">
                                    {{ $lastKinerja->rekomendasi }}
                                </span>

                                <hr class="my-2 opacity-25">

                                <div class="text-start mt-2">
                                    <small class="fw-bold d-block text-muted mb-1"
                                        style="font-size: 0.65rem; text-transform: uppercase;">
                                        Status Terakhir:
                                    </small>

                                    @if ($lastKinerja->status_tindak_lanjut == 'sudah')
                                        <div class="p-2 rounded bg-white border border-success border-opacity-25 shadow-sm">
                                            <span class="badge bg-success mb-1" style="font-size: 0.6rem;">
                                                <i data-feather="check-circle" style="width:10px; height:10px;"></i>
                                                TERVERIFIKASI
                                            </span>
                                            <p class="mb-0 text-dark small fst-italic"
                                                style="font-size: 0.75rem; line-height: 1.2;">
                                                "{{ Str::limit($lastKinerja->deskripsi_tindak_lanjut, 100) }}"
                                            </p>
                                        </div>
                                    @else
                                        <div
                                            class="p-2 rounded bg-white border border-warning border-opacity-25 text-center shadow-sm">
                                            <span class="badge bg-warning text-dark mb-1" style="font-size: 0.6rem;">
                                                <i data-feather="clock" style="width:10px; height:10px;"></i> MENUNGGU
                                                RESPON
                                            </span>
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">
                                                {{ in_array($lastKinerja->huruf_mutu, ['A', 'B']) ? 'Apresiasi pimpinan diperlukan' : 'Pembinaan atasan diperlukan' }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="card bg-light border-0 p-4 d-flex align-items-center justify-content-center w-100"
                                style="max-width: 280px; border-style: dashed !important; border-width: 2px !important;">
                                <span class="text-muted small">Belum ada Data Penilaian</span>
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
