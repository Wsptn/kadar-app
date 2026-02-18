@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Riwayat Kinerja</h2>
                <div class="text-muted">Pengurus: <strong class="text-dark">{{ $pengurus->nama }}</strong></div>
            </div>
            <a href="{{ route('pokok.kinerja.index') }}" class="btn btn-secondary btn-sm">
                <i data-feather="arrow-left" class="me-1"></i> Kembali
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i data-feather="check-circle" class="me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Tgl Penilaian</th>
                                <th style="width: 80px;">Nilai</th>
                                <th style="width: 80px;">Mutu</th>
                                <th>Rekomendasi</th>
                                <th>Catatan</th>
                                <th style="width: 220px;">Status Tindak Lanjut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengurus->kinerja as $k)
                                <tr>
                                    {{-- 1. Tgl Penilaian --}}
                                    <td>
                                        {{ \Carbon\Carbon::parse($k->tanggal_penilaian)->format('d M Y') }}
                                    </td>

                                    {{-- 2. Nilai --}}
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $k->nilai_total }}</span>
                                    </td>

                                    {{-- 3. Mutu --}}
                                    <td>
                                        <strong class="fs-5">{{ $k->huruf_mutu }}</strong>
                                    </td>

                                    {{-- 4. Rekomendasi --}}
                                    <td>
                                        @if ($k->rekomendasi == 'Kinerja Memuaskan')
                                            <span class="badge bg-success">Memuaskan</span>
                                        @elseif($k->rekomendasi == 'Pendampingan')
                                            <span class="badge bg-warning text-dark">Pendampingan</span>
                                        @else
                                            <span class="badge bg-danger">Pembinaan</span>
                                        @endif
                                    </td>

                                    {{-- 5. Catatan --}}
                                    <td class="text-start">
                                        <small class="text-muted">{{ $k->catatan ?? '-' }}</small>
                                    </td>

                                    {{-- 6. Status Tindak Lanjut (Tombol Aksi) --}}
                                    <td>
                                        @if ($k->rekomendasi != 'Kinerja Memuaskan')
                                            @if ($k->status_tindak_lanjut == 'belum')
                                                {{-- JIKA BELUM: MUNCUL TOMBOL MERAH --}}
                                                <form action="{{ route('pokok.kinerja.mark_handled', $k->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Konfirmasi: Apakah Pimpinan SUDAH melakukan pembinaan tatap muka dengan pengurus ini?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-danger btn-sm w-100 shadow-sm">
                                                        <i data-feather="alert-triangle" style="width:12px"></i> Tangani
                                                        Sekarang
                                                    </button>
                                                </form>
                                            @else
                                                {{-- JIKA SUDAH: MUNCUL BADGE HIJAU --}}
                                                <div
                                                    class="border border-success rounded p-1 bg-success bg-opacity-10 text-success">
                                                    <i data-feather="check-circle" style="width:12px"></i> Selesai
                                                    <div style="font-size: 0.7rem">
                                                        {{ \Carbon\Carbon::parse($k->tanggal_tindak_lanjut)->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada riwayat penilaian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.feather) feather.replace();
        });
    </script>
@endsection
