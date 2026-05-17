@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Master Instrumen Penilaian</h1>

        {{-- Breadcrumb --}}
        <div class="bg-light p-2 mb-4 border rounded small">
            <span>Data Master / <span class="text-success fw-semibold">Instrumen Penilaian</span></span>
        </div>

        {{-- Logika Hak Akses --}}
        @php
            $userLevel = Auth::user()->level;
            $hasAccess = $userLevel == 'Admin' || $userLevel == 'Biktren';
        @endphp

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Daftar Instrumen Kinerja</h5>
                    @if ($hasAccess)
                        <a href="{{ route('master.instrumen.create') }}" class="btn btn-success btn-sm">
                            <i data-feather="plus-circle" class="me-1"></i>Tambah Instrumen
                        </a>
                    @endif
                </div>

                @php
                    $totalBobotAktif = $instrumens->where('status', 'aktif')->sum('bobot');
                @endphp
                
                <div class="alert alert-{{ $totalBobotAktif == 100 ? 'info' : 'warning' }}">
                    <strong>Total Bobot Aktif Saat Ini: {{ $totalBobotAktif }}%</strong> 
                    @if($totalBobotAktif != 100)
                        <br> <small>Disarankan total bobot adalah 100% agar perhitungan predikat akurat.</small>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-success text-center">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Aspek Utama</th>
                                <th>Indikator Penilaian</th>
                                <th>Bobot</th>
                                <th>Status</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($instrumens as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item->aspek }}</td>
                                    <td>
                                        <strong>{{ $item->indikator }}</strong><br>
                                        <small class="text-muted">{{ $item->keterangan }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->bobot }}%</td>
                                    <td class="text-center">
                                        @if($item->status == 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($hasAccess)
                                            <div class="btn-group">
                                                <a href="{{ route('master.instrumen.edit', $item->id) }}" class="btn btn-outline-warning btn-sm">
                                                    <i data-feather="edit-2" style="width: 14px;"></i>
                                                </a>
                                                <form action="{{ route('master.instrumen.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus instrumen ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i data-feather="trash-2" style="width: 14px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="badge" style="background-color: #6c757d; color: white; padding: 5px 10px;">
                                                <i class="bi bi-lock-fill"></i> Restricted
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Belum ada data instrumen.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-scripts')
    <script>
        $(document).ready(function() {
            feather.replace();
        });
    </script>
@endpush
