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

        /* Animasi kedip halus untuk badge butuh aksi */
        @keyframes blink {
            50% {
                opacity: 0.6;
            }
        }

        .blink-animation {
            animation: blink 2s infinite;
        }
    </style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Data Kinerja & Rekomendasi</h2>
            </div>
        </div>
        <div class="bg-light p-2 mb-3 border rounded small">
            <span>Data Pokok / <span class="text-success fw-semibold">Kinerja & Rekomendasi</span></span>
        </div>

        {{-- FILTER --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('pokok.kinerja.index') }}" id="filterForm">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            style="width: 250px;" placeholder="Cari Nama...">
                        <select name="status_penilaian" class="form-select auto-submit" style="width: 180px;">
                            <option value="">-- Semua Status --</option>
                            <option value="sudah" {{ request('status_penilaian') == 'sudah' ? 'selected' : '' }}>Sudah
                                Dinilai</option>
                            <option value="belum" {{ request('status_penilaian') == 'belum' ? 'selected' : '' }}>Belum
                                Dinilai</option>
                        </select>
                        <a href="{{ route('pokok.kinerja.index') }}" class="btn btn-outline-secondary"><i
                                data-feather="refresh-cw" style="width: 14px;"></i></a>
                    </div>
                </form>
            </div>
        </div>

        @if (session('error'))
            <div class="alert fade show shadow-sm mb-4" role="alert"
                style="background-color: #f8d7da; border: 1px solid #f5c2c7; border-left: 5px solid #dc3545; color: #842029; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem;">
                <div style="display: flex; align-items: center;">
                    <i data-feather="alert-octagon" style="width: 18px; min-width: 18px; margin-right: 10px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    style="position: static; margin: 0;"></button>
            </div>
        @endif

        {{-- Notifikasi Sukses (Warna Hijau) --}}
        @if (session('success'))
            <div class="alert fade show shadow-sm mb-4" role="alert"
                style="background-color: #d1e7dd; border: 1px solid #badbcc; border-left: 5px solid #198754; color: #0f5132; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem;">
                <div style="display: flex; align-items: center;">
                    <i data-feather="check-circle" style="width: 18px; min-width: 18px; margin-right: 10px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    style="position: static; margin: 0;"></button>
            </div>
        @endif

        {{-- GRID KARTU --}}
        <div class="row">
            @forelse ($pengurus as $p)
                @php $lastKinerja = $p->kinerja->last(); @endphp

                {{-- 3 Kolom Kesamping (col-xl-4) --}}
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm pengurus-card h-100 position-relative">

                        {{-- BADGE INDIKATOR POJOK KANAN ATAS --}}
                        @if ($lastKinerja && in_array($lastKinerja->huruf_mutu, ['C', 'D', 'E']))
                            <div class="position-absolute top-0 end-0 m-2">
                                @if ($lastKinerja->status_tindak_lanjut == 'sudah')
                                    <span class="badge bg-success shadow-sm" title="Sudah Ditangani">
                                        <i data-feather="check-circle" style="width:10px"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge bg-danger shadow-sm blink-animation" title="Segera Cek Riwayat">
                                        <i data-feather="alert-circle" style="width:10px"></i> Butuh Aksi
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                {{-- Foto --}}
                                <img src="{{ $p->foto ? asset('storage/' . $p->foto) : asset('template-admin/img/default-avatar.png') }}"
                                    class="pengurus-photo me-3"
                                    onerror="this.src='{{ asset('template-admin/img/default-avatar.png') }}'">

                                {{-- Info --}}
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="fw-bold mb-1 text-truncate" title="{{ $p->nama }}">{{ $p->nama }}
                                    </h6>

                                    @if ($lastKinerja)
                                        <div class="mb-1">
                                            @if ($lastKinerja->huruf_mutu == 'A')
                                                <span class="badge bg-success text-wrap lh-sm"
                                                    style="font-size: 0.65rem;">Apresiasi & Kaderisasi
                                                    ({{ $lastKinerja->nilai_total }})
                                                </span>
                                            @elseif($lastKinerja->huruf_mutu == 'B')
                                                <span class="badge bg-info text-dark text-wrap lh-sm"
                                                    style="font-size: 0.65rem;">Bimbingan ringan
                                                    ({{ $lastKinerja->nilai_total }})</span>
                                            @elseif($lastKinerja->huruf_mutu == 'C')
                                                <span class="badge bg-warning text-dark text-wrap lh-sm"
                                                    style="font-size: 0.65rem;">Pembinaan Sedang
                                                    ({{ $lastKinerja->nilai_total }})</span>
                                            @elseif($lastKinerja->huruf_mutu == 'D')
                                                <span class="badge text-white text-wrap lh-sm"
                                                    style="background-color: #fd7e14; font-size: 0.65rem;">Pembinaan
                                                    Intensif ({{ $lastKinerja->nilai_total }})</span>
                                            @else
                                                <span class="badge bg-danger text-wrap lh-sm"
                                                    style="font-size: 0.65rem;">Penanganan khusus (Merujuk ke SOP)
                                                    ({{ $lastKinerja->nilai_total }})</span>
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

                            {{-- Tombol Aksi (Hanya Riwayat & Input) --}}
                            <div class="pt-2 border-top">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pokok.kinerja.show', $p->id) }}"
                                        class="btn btn-sm btn-outline-info flex-fill" title="Lihat Detail Riwayat">
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
