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
                <div class="card shadow-sm p-4 text-center">

                    {{-- Foto --}}
                    @if ($pengurus->foto)
                        <img src="{{ asset('storage/' . $pengurus->foto) }}" class="rounded mx-auto d-block mb-3"
                            style="width:180px; height:220px; object-fit:cover;"
                            onerror="this.src='{{ asset('template-admin/img/default-avatar.png') }}'">
                    @else
                        <img src="{{ asset('template-admin/img/default-avatar.png') }}" class="rounded mx-auto d-block mb-3"
                            style="width:180px; height:220px; object-fit:cover;">
                    @endif

                    <h4 class="fw-bold mb-1">{{ $pengurus->nama }}</h4>
                    <p class="text-muted mb-1">NIUP: {{ $pengurus->niup }}</p>

                    @php
                        $statusClass = $pengurus->status == 'aktif' ? 'bg-success' : 'bg-danger';
                        $statusText = $pengurus->status == 'aktif' ? 'Aktif' : 'Nonaktif';
                    @endphp

                    <span class="badge {{ $statusClass }} px-3 py-2">
                        {{ $statusText }}
                    </span>

                </div>
            </div>

            {{-- DATA DETAIL --}}
            <div class="col-lg-8">
                <div class="card shadow-sm p-4 position-relative">

                    {{-- Tombol X di pojok kanan atas --}}
                    <a href="{{ route('pokok.pengurus.index') }}"
                        class="btn btn-light position-absolute d-flex align-items-center justify-content-center"
                        style="top:15px; right:15px; border-radius:50%; width:35px; height:35px; padding:0; font-weight:bold;">
                        X
                    </a>

                    {{-- Header --}}
                    <h4 class="fw-bold mb-1">{{ $pengurus->nama }}</h4>
                    <p class="text-muted">NIUP: {{ $pengurus->niup }}</p>

                    <hr>

                    {{-- Layout kiri label - kanan value --}}
                    <div class="row">

                        {{-- ===== BAGIAN 1: LOKASI (WILAYAH -> DAERAH -> ENTITAS DAERAH -> KAMAR) ===== --}}

                        {{-- Wilayah --}}
                        <div class="col-md-12 mb-3 d-flex justify-content-between">
                            <label class="fw-semibold" style="width: 250px;">Wilayah</label>
                            <p class="m-0 ms-2 flex-grow-1">{{ $pengurus->wilayah->nama_wilayah ?? '-' }}</p>
                        </div>

                        {{-- Daerah --}}
                        <div class="col-md-12 mb-3 d-flex justify-content-between">
                            <label class="fw-semibold" style="width: 250px;">Daerah</label>
                            <p class="m-0 ms-2 flex-grow-1">{{ $pengurus->daerah->nama_daerah ?? '-' }}</p>
                        </div>

                        {{-- === ENTITAS DAERAH === --}}
                        <div class="col-md-12 mb-3 d-flex justify-content-between">
                            <label class="fw-semibold" style="width: 250px;">Entitas Daerah</label>
                            <p class="m-0 ms-2 flex-grow-1">
                                {{ $pengurus->entitas_daerah ?? '-' }}
                            </p>
                        </div>
                        {{-- ==================================== --}}

                        {{-- Kamar --}}
                        <div class="col-md-12 mb-3 d-flex justify-content-between">
                            <label class="fw-semibold" style="width: 250px;">Kamar</label>
                            <p class="m-0 ms-2 flex-grow-1">{{ $pengurus->kamar->nomor_kamar ?? '-' }}</p>
                        </div>


                        {{-- ===== BAGIAN 2: DATA LAINNYA (ARRAY LOOP) ===== --}}
                        @php
                            $fields = [
                                ['Entitas', $pengurus->entitas->nama_entitas ?? '-'],
                                ['Jabatan', $pengurus->jabatan->nama_jabatan ?? '-'],
                                ['Jenis Jabatan', $pengurus->jenisJabatan->jenis_jabatan ?? '-'],
                                ['Grade Jabatan', $pengurus->gradeJabatan->grade ?? '-'],
                                ['SK Kepengurusan', $pengurus->sk_kepengurusan ?? '-'],
                                ['Fungsional Tugas', $pengurus->fungsionalTugas->tugas ?? '-'],
                                ['Rangkap Internal', $pengurus->rangkapInternal->internal ?? '-'],
                                ['Rangkap Eksternal', $pengurus->rangkapEksternal->eksternal ?? '-'],
                                ['Pendidikan', $pengurus->pendidikan->nama_pendidikan ?? '-'],
                                ['Angkatan', $pengurus->angkatan->angkatan ?? '-'],
                                ['Status', ucfirst($pengurus->status)],
                            ];
                        @endphp

                        @foreach ($fields as $row)
                            <div class="col-md-12 mb-3 d-flex justify-content-between">
                                <label class="fw-semibold" style="width: 250px;">{{ $row[0] }}</label>
                                <p class="m-0  ms-2 flex-grow-1">{{ $row[1] }}</p>
                            </div>
                        @endforeach

                        <hr>
                        <h6 class="fw-bold mb-3">Berkas Lampiran</h6>

                        {{-- FILES --}}
                        <div class="col-md-12 mb-3 d-flex justify-content-between">
                            <label class="fw-semibold" style="width: 250px;">Berkas SK Pengurus</label>
                            <p class="m-0  ms-2 flex-grow-1">
                                @if ($pengurus->berkas_sk_pengurus)
                                    <a href="{{ asset('storage/' . $pengurus->berkas_sk_pengurus) }}" target="_blank"
                                        class="text-decoration-none">
                                        <i class="bi bi-file-earmark-text me-1"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Tidak ada file</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-12 mb-3 d-flex justify-content-between">
                            <label class="fw-semibold" style="width: 250px;">Berkas Surat Tugas</label>
                            <p class="m-0  ms-2 flex-grow-1">
                                @if ($pengurus->berkas_surat_tugas)
                                    <a href="{{ asset('storage/' . $pengurus->berkas_surat_tugas) }}" target="_blank"
                                        class="text-decoration-none">
                                        <i class="bi bi-file-earmark-text me-1"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Tidak ada file</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-12 mb-3 d-flex justify-content-between">
                            <label class="fw-semibold" style="width: 250px;">Berkas PLT</label>
                            <p class="m-0  ms-2 flex-grow-1">
                                @if ($pengurus->berkas_plt)
                                    <a href="{{ asset('storage/' . $pengurus->berkas_plt) }}" target="_blank"
                                        class="text-decoration-none">
                                        <i class="bi bi-file-earmark-text me-1"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Tidak ada file</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-12 mb-3 d-flex justify-content-between">
                            <label class="fw-semibold" style="width: 250px;">Berkas Lain</label>
                            <p class="m-0  ms-2 flex-grow-1">
                                @if ($pengurus->berkas_lain)
                                    <a href="{{ asset('storage/' . $pengurus->berkas_lain) }}" target="_blank"
                                        class="text-decoration-none">
                                        <i class="bi bi-file-earmark-text me-1"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Tidak ada file</span>
                                @endif
                            </p>
                        </div>

                    </div>

                    {{-- BUTTON EDIT + HAPUS --}}
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('pokok.pengurus.edit', $pengurus->id) }}" class="btn btn-warning me-2">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>

                        {{-- Hanya Admin/Wilayah yang boleh hapus (sesuai logic controller) --}}
                        @if (!Auth::user()->isDaerah())
                            <form method="POST" action="{{ route('pokok.pengurus.destroy', $pengurus->id) }}"
                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">
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
