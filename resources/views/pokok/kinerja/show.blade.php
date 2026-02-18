@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Riwayat Kinerja</h2>
                <div class="text-muted">Pengurus: <strong>{{ $pengurus->nama }}</strong> ({{ $pengurus->niup }})</div>
            </div>
            <a href="{{ route('pokok.kinerja.index') }}" class="btn btn-secondary btn-sm">
                <i data-feather="arrow-left" class="me-1"></i> Kembali
            </a>
        </div>

        {{-- Tabel Riwayat --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal Penilaian</th>
                                <th class="text-center">Nilai Total</th>
                                <th class="text-center">Mutu</th>
                                <th>Rekomendasi</th>
                                <th>Catatan</th>
                                {{-- <th class="text-center">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengurus->kinerja as $k)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($k->tanggal_penilaian)->format('d F Y') }}
                                        <br>
                                        <small
                                            class="text-muted">{{ \Carbon\Carbon::parse($k->created_at)->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary"
                                            style="font-size: 0.9rem">{{ $k->nilai_total }}</span>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $k->huruf_mutu }}</strong>
                                    </td>
                                    <td>
                                        @if ($k->rekomendasi == 'Kinerja Memuaskan')
                                            <span class="badge bg-success">Memuaskan</span>
                                        @elseif($k->rekomendasi == 'Pendampingan')
                                            <span class="badge bg-warning text-dark">Pendampingan</span>
                                        @else
                                            <span class="badge bg-danger">Pembinaan</span>
                                        @endif
                                    </td>
                                    <td>{{ $k->catatan ?? '-' }}</td>
                                    {{-- 
                            <td class="text-center">
                                <a href="#" class="btn btn-sm btn-light" title="Cetak"><i data-feather="printer"></i></a>
                            </td> 
                            --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i data-feather="inbox" style="width: 32px; height: 32px;" class="mb-2"></i><br>
                                        Belum ada riwayat penilaian untuk pengurus ini.
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
