@extends('layouts.app')

@section('this-page-contain')

<style>
    /* Custom Styling untuk Nav Tabs agar warnanya dinamis saat diklik */
    .nav-tabs .nav-link {
        color: #212529 !important; /* Warna hitam elegan untuk tab yang tidak aktif */
        font-weight: 600;
        transition: all 0.2s ease-in-out;
    }
    
    .nav-tabs .nav-link:hover {
        color: #198754 !important; /* Warna hijau saat di-hover */
    }

    .nav-tabs .nav-link.active {
        color: #198754 !important; /* Warna hijau untuk tab yang sedang aktif */
        border-bottom: 3px solid #198754 !important; /* Garis bawah hijau agar lebih tegas */
        background-color: transparent !important;
    }
</style>

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
                                {{-- Judul & Tanggal Penilaian --}}
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted fw-bold"
                                        style="font-size: 0.65rem; text-transform: uppercase;">
                                        Hasil Kinerja Terakhir
                                    </small>
                                    <small class="text-secondary fw-semibold" style="font-size: 0.6rem;">
                                        <i data-feather="calendar" style="width: 10px; height: 10px; margin-top: -2px;"></i>
                                        {{ \Carbon\Carbon::parse($lastKinerja->tanggal_penilaian)->translatedFormat('d M Y') }}
                                    </small>
                                </div>

                                {{-- Skor Total --}}
                                <div
                                    class="display-6 fw-bold my-1 
            {{ $lastKinerja->nilai_total >= 75 ? 'text-primary' : ($lastKinerja->nilai_total >= 70 ? 'text-warning' : 'text-danger') }}">
                                    {{ $lastKinerja->nilai_total }}
                                </div>

                                {{-- Rekomendasi --}}
                                <span
                                    class="badge {{ $lastKinerja->nilai_total >= 75 ? 'bg-success' : ($lastKinerja->nilai_total >= 70 ? 'bg-warning text-dark' : 'bg-danger') }} mb-2 text-wrap">
                                    {{ $lastKinerja->rekomendasi }}
                                </span>

                                <hr class="my-2 opacity-25">

                                {{-- Status Tindak Lanjut --}}
                                <div class="text-start mt-2">
                                    <small class="fw-bold d-block text-muted mb-1"
                                        style="font-size: 0.65rem; text-transform: uppercase;">
                                        Status Terakhir:
                                    </small>

                                    @if ($lastKinerja->status_tindak_lanjut == 'sudah')
                                        <div class="p-2 rounded bg-white border border-success border-opacity-25 shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge bg-success" style="font-size: 0.6rem;">
                                                    <i data-feather="check-circle" style="width:10px; height:10px;"></i>
                                                    TERVERIFIKASI
                                                </span>
                                                {{-- Tanggal Verifikasi/Tindak Lanjut --}}
                                                @if ($lastKinerja->tanggal_tindak_lanjut)
                                                    <small class="text-muted" style="font-size: 0.55rem;">
                                                        {{ \Carbon\Carbon::parse($lastKinerja->tanggal_tindak_lanjut)->translatedFormat('d M Y') }}
                                                    </small>
                                                @endif
                                            </div>

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

            {{-- DATA DETAIL & RIWAYAT (Kanan) --}}
            <div class="col-lg-8">
                <div class="card shadow-sm p-0 position-relative h-100">

                    {{-- Tombol X (Tutup) --}}
                    <div class="position-absolute top-0 end-0 p-3 z-3">
                        <a href="{{ route('pokok.pengurus.index') }}"
                            class="btn btn-light btn-sm shadow-sm border text-secondary" data-bs-toggle="tooltip"
                            title="Kembali">
                            <i class="bi bi-x-lg fw-bold"></i> Tutup
                        </a>
                    </div>

                    {{-- NAV TABS UTAMA --}}
                    <div class="card-header bg-white pt-4 pb-0 border-bottom">
                        <ul class="nav nav-tabs border-bottom-0" id="mainTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail-pane" type="button" role="tab" aria-controls="detail-pane" aria-selected="true">
                                    <i class="bi bi-person-lines-fill me-2"></i> Informasi Detail
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-pane" type="button" role="tab" aria-controls="riwayat-pane" aria-selected="false">
                                    <i class="bi bi-clock-history me-2"></i> Riwayat Pengurus
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="mainTabContent">

                            {{-- TAB 1: INFORMASI DETAIL --}}
                            <div class="tab-pane fade show active" id="detail-pane" role="tabpanel" aria-labelledby="detail-tab" tabindex="0">


                    {{-- BAGIAN 1: LOKASI --}}
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Wilayah</div>
                        <div class="col-sm-8 fw-bold text-dark">: {{ $pengurus->kamar?->daerah?->wilayah?->nama_wilayah ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Daerah</div>
                        <div class="col-sm-8 fw-bold text-dark">: {{ $pengurus->kamar?->daerah?->nama_daerah ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Entitas Daerah</div>
                        <div class="col-sm-8">: {{ $pengurus->entitas_daerah ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Kamar</div>
                        <div class="col-sm-8">: {{ $pengurus->kamar?->nomor_kamar ?? '-' }}</div>
                    </div>

                    <hr class="my-3 text-muted opacity-50">

                    {{-- BAGIAN 2: KELEMBAGAAN & TUGAS --}}
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Entitas</div>
                        <div class="col-sm-8">: {{ $pengurus->strukturJabatan->entitas ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Jabatan</div>
                        <div class="col-sm-8">: {{ $pengurus->strukturJabatan->jabatan ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Jenis & Grade</div>
                        <div class="col-sm-8">
                            : {{ $pengurus->strukturJabatan->jenis_jabatan ?? '-' }} /
                            {{ $pengurus->strukturJabatan->grade ?? '-' }}
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
                                                {{ $ft->nama_tugas }}
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
                        <div class="col-sm-4 fw-semibold text-muted">Tugas Internal</div>
                        <div class="col-sm-8">: {{ $pengurus->internalTugas->first()->nama_tugas ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Tugas Eksternal</div>
                        <div class="col-sm-8">: {{ $pengurus->eksternalTugas->first()->nama_tugas ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold text-muted">Pendidikan</div>
                        <div class="col-sm-8">: {{ $pengurus->pendidikan->nama_pendidikan ?? '-' }}</div>
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



                            </div> {{-- Tutup Tab 1: Detail --}}

                            {{-- TAB 2: RIWAYAT PENGURUS --}}
                            <div class="tab-pane fade" id="riwayat-pane" role="tabpanel" aria-labelledby="riwayat-tab" tabindex="0">
                    
                    {{-- Nav Tabs --}}
                    <ul class="nav nav-tabs mb-4" id="riwayatTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active px-4" id="jabatan-tab" data-bs-toggle="tab" data-bs-target="#jabatan-tab-pane" type="button" role="tab" aria-controls="jabatan-tab-pane" aria-selected="true">
                                <i class="bi bi-diagram-3 me-2"></i>Jabatan Struktural
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-4" id="tugas-tab" data-bs-toggle="tab" data-bs-target="#tugas-tab-pane" type="button" role="tab" aria-controls="tugas-tab-pane" aria-selected="false">
                                <i class="bi bi-card-checklist me-2"></i>Penugasan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-4" id="pendidikan-tab" data-bs-toggle="tab" data-bs-target="#pendidikan-tab-pane" type="button" role="tab" aria-controls="pendidikan-tab-pane" aria-selected="false">
                                <i class="bi bi-mortarboard me-2"></i>Pendidikan
                            </button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="riwayatTabContent">
                        
                        {{-- Tab Panel: Riwayat Jabatan --}}
                        <div class="tab-pane fade show active" id="jabatan-tab-pane" role="tabpanel" aria-labelledby="jabatan-tab" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th>Nama Jabatan</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th width="15%" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pengurus->riwayatJabatans as $index => $rj)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="fw-semibold">{{ $rj->strukturJabatan->jabatan ?? '-' }} - {{ $rj->strukturJabatan->entitas ?? '-' }}</td>
                                                <td>{{ $rj->tgl_mulai ? \Carbon\Carbon::parse($rj->tgl_mulai)->translatedFormat('d F Y') : '-' }}</td>
                                                <td>{{ $rj->tgl_selesai ? \Carbon\Carbon::parse($rj->tgl_selesai)->translatedFormat('d F Y') : 'Sekarang' }}</td>
                                                <td class="text-center">
                                                    @if($rj->status == 'aktif')
                                                        <span class="badge bg-success bg-opacity-75 px-3 py-2 rounded-pill">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-75 px-3 py-2 rounded-pill">Selesai</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted fst-italic py-3">Belum ada riwayat jabatan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab Panel: Riwayat Tugas --}}
                        <div class="tab-pane fade" id="tugas-tab-pane" role="tabpanel" aria-labelledby="tugas-tab" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th>Nama Tugas</th>
                                            <th>Jenis Tugas</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th width="15%" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pengurus->riwayatTugas as $index => $rt)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="fw-semibold">{{ $rt->masterTugas->nama_tugas ?? '-' }}</td>
                                                <td><span class="badge bg-info bg-opacity-50 text-dark">{{ ucfirst($rt->masterTugas->jenis_tugas ?? '-') }}</span></td>
                                                <td>{{ $rt->tgl_mulai ? \Carbon\Carbon::parse($rt->tgl_mulai)->translatedFormat('d F Y') : '-' }}</td>
                                                <td>{{ $rt->tgl_selesai ? \Carbon\Carbon::parse($rt->tgl_selesai)->translatedFormat('d F Y') : 'Sekarang' }}</td>
                                                <td class="text-center">
                                                    @if($rt->status == 'aktif')
                                                        <span class="badge bg-success bg-opacity-75 px-3 py-2 rounded-pill">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-75 px-3 py-2 rounded-pill">Selesai</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted fst-italic py-3">Belum ada riwayat penugasan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab Panel: Riwayat Pendidikan --}}
                        <div class="tab-pane fade" id="pendidikan-tab-pane" role="tabpanel" aria-labelledby="pendidikan-tab" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th>Nama Pendidikan</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th width="15%" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pengurus->riwayatPendidikan as $index => $rp)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="fw-semibold">{{ $rp->pendidikan->nama_pendidikan ?? '-' }}</td>
                                                <td>{{ $rp->tanggal_mulai ? \Carbon\Carbon::parse($rp->tanggal_mulai)->translatedFormat('d F Y') : '-' }}</td>
                                                <td>{{ $rp->tanggal_selesai ? \Carbon\Carbon::parse($rp->tanggal_selesai)->translatedFormat('d F Y') : 'Sekarang' }}</td>
                                                <td class="text-center">
                                                    @if($rp->status == 'aktif')
                                                        <span class="badge bg-success bg-opacity-75 px-3 py-2 rounded-pill">Aktif Studi</span>
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-75 px-3 py-2 rounded-pill">Selesai</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted fst-italic py-3">Belum ada riwayat pendidikan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                            </div> {{-- Tutup Tab 2: Riwayat --}}
                            
                        </div> {{-- Tutup Tab Content Utama --}}
                        
                        {{-- BUTTON EDIT + HAPUS (Tetap Muncul di Bawah) --}}
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

                    </div> {{-- Tutup Card Body Kanan --}}
                </div> {{-- Tutup Card Kanan --}}
            </div> {{-- Tutup col-lg-8 --}}
    </div>
@endsection
