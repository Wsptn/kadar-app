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

        <div class="row">
            @foreach ($pengurus as $p)
                @php $lastKinerja = $p->kinerja->last(); @endphp
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm pengurus-card h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ $p->foto ? asset('storage/' . $p->foto) : asset('template-admin/img/default-avatar.png') }}"
                                    class="pengurus-photo me-3">
                                <div class="overflow-hidden">
                                    <h6 class="fw-bold mb-1 text-truncate">{{ $p->nama }}</h6>
                                    @if ($lastKinerja)
                                        <span class="badge bg-success small" style="font-size:0.6rem">Sudah Dinilai</span>
                                        <div class="text-primary small fw-bold mt-1" style="font-size:0.65rem">
                                            {{ $lastKinerja->rekomendasi }}</div>
                                    @else
                                        <span class="badge bg-secondary small" style="font-size:0.6rem">Belum Dinilai</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 pt-2 border-top">
                                @if ($lastKinerja)
                                    <button class="btn btn-sm btn-outline-secondary w-100 disabled">Selesai</button>
                                @else
                                    <a href="{{ route('pokok.kinerja.create', ['pengurus_id' => $p->id]) }}"
                                        class="btn btn-sm btn-primary w-100">Input Nilai</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $pengurus->links('pagination::bootstrap-5') }}
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
