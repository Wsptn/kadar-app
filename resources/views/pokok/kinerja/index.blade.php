@extends('layouts.app')

@section('this-page-style')
    <style>
        .pengurus-card {
            transition: transform 0.2s;
            border: none;
        }

        .pengurus-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .pengurus-photo {
            width: 90px;
            height: 110px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h2 class="mb-1">Input Kinerja Pengurus</h2>

        <div class="bg-light p-2 mb-3 border rounded small">
            <span>Data Pokok / <span class="text-primary fw-semibold">Kinerja & Tindak Lanjut</span></span>
        </div>

        {{-- FILTER CARD --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('pokok.kinerja.index') }}" id="filterForm">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        {{-- Search --}}
                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text bg-white"><i data-feather="search"
                                    style="width: 14px;"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari Nama...">
                        </div>

                        {{-- Filter Status Penilaian --}}
                        <select name="status_penilaian" class="form-select auto-submit" style="width: 180px;">
                            <option value="">-- Status Nilai --</option>
                            <option value="sudah" {{ request('status_penilaian') == 'sudah' ? 'selected' : '' }}>Sudah
                                Dinilai</option>
                            <option value="belum" {{ request('status_penilaian') == 'belum' ? 'selected' : '' }}>Belum
                                Dinilai</option>
                        </select>

                        {{-- Reset --}}
                        <a href="{{ route('pokok.kinerja.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="refresh-cw" style="width: 14px;"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- GRID PENGURUS --}}
        <div class="row">
            @forelse ($pengurus as $p)
                @php
                    $lastKinerja = $p->kinerja->last();
                @endphp

                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm pengurus-card h-100 position-relative">

                        {{-- === BADGE STATUS PENANGANAN (POJOK KANAN ATAS) === --}}
                        @if ($lastKinerja && $lastKinerja->rekomendasi != 'Kinerja Memuaskan')
                            <div class="position-absolute top-0 end-0 m-2">
                                @if ($lastKinerja->status_tindak_lanjut == 'sudah')
                                    <span class="badge bg-success shadow-sm" title="Masalah sudah ditangani">
                                        <i data-feather="check-circle" style="width:10px"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge bg-danger shadow-sm" title="Perlu segera ditangani">
                                        <i data-feather="alert-circle" style="width:10px"></i> Butuh Aksi
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                {{-- FOTO --}}
                                <div class="me-3 flex-shrink-0">
                                    <img src="{{ $p->foto ? asset('storage/' . $p->foto) : asset('template-admin/img/default-avatar.png') }}"
                                        class="pengurus-photo"
                                        onerror="this.onerror=null; this.src='{{ asset('template-admin/img/default-avatar.png') }}'">
                                </div>

                                {{-- INFO SINGKAT --}}
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="fw-bold mb-1 text-truncate" title="{{ $p->nama }}">{{ $p->nama }}
                                    </h6>

                                    @if ($lastKinerja)
                                        {{-- Badge Hasil Rekomendasi --}}
                                        <div class="mb-1">
                                            @if ($lastKinerja->rekomendasi == 'Kinerja Memuaskan')
                                                <span class="badge bg-primary text-wrap lh-sm" style="font-size: 0.65rem;">
                                                    Memuaskan ({{ $lastKinerja->nilai_total }})
                                                </span>
                                            @elseif($lastKinerja->rekomendasi == 'Pendampingan')
                                                <span class="badge bg-warning text-dark text-wrap lh-sm"
                                                    style="font-size: 0.65rem;">
                                                    Pendampingan ({{ $lastKinerja->nilai_total }})
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-wrap lh-sm" style="font-size: 0.65rem;">
                                                    Pembinaan ({{ $lastKinerja->nilai_total }})
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 0.6rem">
                                            {{ \Carbon\Carbon::parse($lastKinerja->tanggal_penilaian)->format('d M Y') }}
                                        </small>
                                    @else
                                        <span class="badge bg-secondary small">Belum Dinilai</span>
                                    @endif
                                </div>
                            </div>

                            {{-- AREA AKSI (TINDAK LANJUT & TOMBOL) --}}
                            <div class="pt-2 border-top">

                                {{-- LOGIKA TINDAK LANJUT --}}
                                @if ($lastKinerja && $lastKinerja->rekomendasi != 'Kinerja Memuaskan')
                                    @if ($lastKinerja->status_tindak_lanjut == 'belum')
                                        {{-- TOMBOL MERAH: TANDAI SUDAH DITANGANI --}}
                                        <form action="{{ route('pokok.kinerja.mark_handled', $lastKinerja->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Pimpinan sudah memanggil dan membina pengurus ini secara tatap muka? Status akan diubah menjadi SUDAH DITANGANI.')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-danger btn-sm w-100 mb-2 shadow-sm">
                                                <i data-feather="check-square" style="width: 12px;"></i> Tandai Sudah
                                                Ditangani
                                            </button>
                                        </form>
                                    @else
                                        {{-- INFO HIJAU: SUDAH DITANGANI --}}
                                        <div class="alert alert-success p-1 mb-2 text-center" style="font-size: 0.7rem;">
                                            <i data-feather="check" style="width: 10px;"></i>
                                            Ditangani:
                                            {{ \Carbon\Carbon::parse($lastKinerja->tanggal_tindak_lanjut)->format('d/m/y') }}
                                        </div>
                                    @endif
                                @endif

                                {{-- TOMBOL UTAMA (RIWAYAT & INPUT) --}}
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pokok.kinerja.show', $p->id) }}"
                                        class="btn btn-sm btn-outline-info flex-fill" title="Lihat Riwayat">
                                        <i data-feather="clock" style="width: 14px;"></i> Riwayat
                                    </a>

                                    <a href="{{ route('pokok.kinerja.create', ['pengurus_id' => $p->id]) }}"
                                        class="btn btn-sm btn-outline-success flex-fill" title="Input Nilai Baru">
                                        Input <i data-feather="edit-3" style="width: 14px;" class="ms-1"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i data-feather="inbox" style="width: 40px; height: 40px; color: #dee2e6;"></i>
                    <p class="mt-2">Tidak ada data pengurus ditemukan.</p>
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $pengurus->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') feather.replace();
            const form = document.getElementById('filterForm');
            document.querySelectorAll('.auto-submit').forEach(s => s.addEventListener('change', () => form
            .submit()));
        });
    </script>
@endsection
