@extends('layouts.app')

@section('this-page-style')
    <style>
        .pengurus-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .pengurus-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1) !important;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
        }
    </style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Administrasi User</h1>

        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Notifikasi Error --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TOOLBAR: Tombol Tambah & Pencarian --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row gy-3 align-items-center">

                    {{-- 1. Tombol Tambah (Kiri) --}}
                    <div class="col-12 col-md-4">
                        @auth
                            @if (auth()->user()->isAdmin() || auth()->user()->isBiktren())
                                <a href="{{ route('user.create') }}" class="btn btn-success d-inline-flex align-items-center">
                                    <i data-feather="plus" class="me-1"></i> Tambah Pengguna
                                </a>
                            @endif
                        @endauth
                    </div>

                    {{-- 2. Form Pencarian (Kanan) --}}
                    <div class="col-12 col-md-8 d-flex justify-content-md-end">
                        <form action="{{ route('user.index') }}" method="GET" id="searchForm">
                            <div class="input-group" style="max-width: 230px;">
                                <span class="input-group-text bg-white">
                                    <i data-feather="search" style="width: 16px; height: 16px;"></i>
                                </span>
                                <input type="text" name="search" id="searchInput" class="form-control border-start-0"
                                    placeholder="Cari User..." value="{{ request('search') }}" autocomplete="off">
                                <button class="btn btn-success" type="submit">Cari</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- Tabel User (TAMPILAN TETAP) --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Pengguna</th>
                        <th class="text-center" style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if ($user->status === 'aktif')
                                    <span class="badge bg-success status-badge ms-2">Aktif</span>
                                @else
                                    <span class="badge bg-secondary status-badge ms-2">Nonaktif</span>
                                @endif

                                {{-- LOGIKA TAMPILAN LEVEL --}}
                                @if ($user->isWilayah() && $user->wilayah)
                                    <span class="badge bg-primary status-badge ms-1">
                                        {{ $user->wilayah->nama_wilayah }}
                                    </span>
                                @elseif ($user->isDaerah() && $user->daerah)
                                    <span class="badge bg-warning text-dark status-badge ms-1">
                                        {{ $user->daerah->nama_daerah }}
                                    </span>
                                @else
                                    <span class="badge bg-info status-badge ms-1">{{ $user->level }}</span>
                                @endif

                                <br>
                                <small class="text-muted">{{ '@' . $user->username }}</small>
                                <br>
                                <small class="text-muted">Dibuat: {{ $user->created_at->format('d M Y H:i') }}</small>
                            </td>

                            {{-- KOLOM AKSI --}}
                            <td class="text-center">
                                @if (Auth::user()->isAdmin() || Auth::user()->isBiktren())
                                    {{-- Reset Password --}}
                                    <a href="{{ route('user.reset-password', $user->id) }}"
                                        class="btn btn-sm btn-outline-warning me-1" title="Ganti Password">
                                        <i data-feather="key"></i>
                                    </a>

                                    {{-- Nonaktifkan/Aktifkan --}}
                                    @if (Auth::user()->isAdmin())
                                        @if ($user->id !== Auth::id())
                                            <form action="{{ route('user.toggle-status', $user->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('PATCH')

                                                @if ($user->status === 'aktif')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menonaktifkan akun {{ $user->name }}?')"
                                                        title="Nonaktifkan User">
                                                        <i data-feather="user-x"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                        onclick="return confirm('Aktifkan kembali akun {{ $user->name }}?')"
                                                        title="Aktifkan User">
                                                        <i data-feather="user-check"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-light border" disabled title="Akun Anda Sendiri">
                                                <i data-feather="user"></i>
                                            </button>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-muted small fst-italic">No Action</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada data user</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-end mt-4">
            {{-- withQueryString() PENTING agar pencarian tidak hilang saat pindah halaman --}}
            {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Init Icons
            if (typeof feather !== 'undefined') feather.replace();

            // LOGIKA PENCARIAN OTOMATIS
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            let typingTimer;
            const doneTypingInterval = 800;

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    clearTimeout(typingTimer);

                    // Ambil nilai input
                    const val = searchInput.value;

                    // Jika user selesai mengetik (debounce)
                    typingTimer = setTimeout(function() {
                        // Submit jika karakter >= 3 atau kosong (untuk reset)
                        if (val.length >= 3 || val.length === 0) {
                            searchForm.submit();
                        }
                    }, doneTypingInterval);
                });

                // Mencegah submit form saat menekan Enter jika karakter kurang dari 3 (Opsional)
                searchInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        searchForm.submit();
                    }
                });
            }
        });
    </script>
@endsection
