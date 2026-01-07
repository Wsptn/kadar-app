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

        {{-- Tombol Tambah --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row gy-3 align-items-center">
                    <div class="col-12 col-md-4">
                        @auth
                            @if (auth()->user()->isAdmin() || auth()->user()->isBiktren())
                                <a href="{{ route('user.create') }}" class="btn btn-success d-inline-flex align-items-center">
                                    <i data-feather="plus" class="me-1"></i> Tambah Pengguna
                                </a>
                            @endif
                        @endauth
                    </div>
                    <div class="col-12 col-md-8">
                        {{-- Filter Code (Placeholder) --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel User --}}
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

                                {{-- LOGIKA TAMPILAN LEVEL DISESUAIKAN --}}
                                @if ($user->isWilayah() && $user->wilayah)
                                    {{-- Jika Level Wilayah -> Tampilkan Nama Wilayah --}}
                                    <span class="badge bg-primary status-badge ms-1">
                                        {{ $user->wilayah->nama_wilayah }}
                                    </span>
                                @elseif ($user->isDaerah() && $user->daerah)
                                    {{-- Jika Level Daerah -> Tampilkan Nama Daerah --}}
                                    <span class="badge bg-warning text-dark status-badge ms-1">
                                        {{ $user->daerah->nama_daerah }}
                                    </span>
                                @else
                                    {{-- Jika Admin/Biktren -> Tampilkan Level Asli --}}
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
                                    {{-- 1. Tombol Reset Password --}}
                                    <a href="{{ route('user.reset-password', $user->id) }}"
                                        class="btn btn-sm btn-outline-warning me-1" title="Ganti Password User Ini">
                                        <i data-feather="key"></i>
                                    </a>

                                    {{-- 2. Tombol Nonaktifkan/Aktifkan --}}
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
                                            {{-- Jika akun sendiri (Disabled Button) --}}
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
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof feather !== 'undefined') feather.replace();
        });
    </script>
@endsection
