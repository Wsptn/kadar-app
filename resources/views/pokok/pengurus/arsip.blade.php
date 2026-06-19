@extends('layouts.app')

@section('this-page-contain')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h2 class="mb-1">Arsip Pengurus (Terhapus)</h2>
            <div class="text-muted">Daftar pengurus yang sudah dihapus beserta rekam jejak penilaiannya.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pokok.pengurus.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i data-feather="arrow-left" class="me-1" style="width: 16px;"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle text-center" style="font-size: 0.9rem;">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama & NIUP</th>
                            <th>Jabatan Terakhir</th>
                            <th>Status Penilaian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengurus as $index => $p)
                            <tr>
                                <td>{{ $pengurus->firstItem() + $index }}</td>
                                <td class="text-start">
                                    <strong>{{ $p->nama }}</strong><br>
                                    <small class="text-muted">{{ $p->niup }}</small>
                                </td>
                                <td>
                                    @if($p->strukturJabatan)
                                        {{ $p->strukturJabatan->jabatan }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $p->kinerja->count() > 0 ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $p->kinerja->count() }} Evaluasi
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('pokok.kinerja.show', $p->id) }}" class="btn btn-sm btn-success shadow-sm" title="Lihat Rapor">
                                        <i data-feather="eye" style="width:14px"></i> Lihat Kinerja
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data pengurus di arsip.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $pengurus->links('pagination::bootstrap-5') }}
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
