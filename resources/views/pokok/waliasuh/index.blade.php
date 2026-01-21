@extends('layouts.app')

@section('this-page-style')
    <style>
        .pengurus-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            overflow: hidden;
        }

        .pengurus-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .pengurus-photo-wrapper {
            flex-shrink: 0;
        }

        .pengurus-photo {
            width: 90px;
            height: 110px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .action-area {
            margin-top: 1rem;
            border-top: 1px solid #f3f4f6;
            padding-top: 0.75rem;
            text-align: right;
        }
    </style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Data Wali Asuh</h2>
            </div>
        </div>

        <div class="bg-light p-2 mb-3 border rounded small">
            <span>Data Pokok / <span class="text-success fw-semibold">Wali Asuh</span></span>
        </div>

        {{-- Search Filter --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <form action="{{ route('pokok.waliasuh.index') }}" method="GET"
                    class="d-flex align-items-center flex-wrap gap-2">

                    <div class="input-group" style="width: auto; flex-grow: 1; min-width: 200px;">
                        <span class="input-group-text bg-white"><i data-feather="search" style="width: 16px;"></i></span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control"
                            placeholder="Cari nama atau NIUP...">
                    </div>

                    <button type="submit" class="btn btn-success d-flex align-items-center">
                        <i data-feather="search" class="me-1" style="width: 16px;"></i> Cari
                    </button>

                    @if (!empty($search))
                        <a href="{{ route('pokok.waliasuh.index') }}" class="btn btn-secondary d-flex align-items-center">
                            <i data-feather="refresh-cw" class="me-1" style="width: 16px;"></i> Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Grid Data --}}
        <div class="row">
            @forelse ($waliasuh as $p)
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm pengurus-card h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">

                                {{-- FOTO --}}
                                <div class="pengurus-photo-wrapper me-3">
                                    <img src="{{ $p->foto ? asset('storage/' . $p->foto) : asset('template-admin/img/default-avatar.png') }}"
                                        class="pengurus-photo" alt="{{ $p->nama }}"
                                        onerror="this.src='{{ asset('template-admin/img/default-avatar.png') }}'">
                                </div>

                                {{-- INFO UTAMA --}}
                                <div class="flex-grow-1 overflow-hidden">
                                    {{-- Nama --}}
                                    <h6 class="fw-bold mb-1 text-truncate" title="{{ $p->nama }}">{{ $p->nama }}
                                    </h6>

                                    {{-- NIUP --}}
                                    <div class="mb-2">
                                        <span class="badge bg-light text-dark border">{{ $p->niup ?? '-' }}</span>
                                    </div>

                                    {{-- Fungsional Tugas (PERBAIKAN MULTI-SELECT) --}}
                                    <div class="small text-success fw-bold mb-1" title="Fungsional Tugas">
                                        <i data-feather="briefcase" style="width: 12px;" class="me-1"></i>

                                        @if ($p->fungsionalTugas->count() > 0)
                                            @foreach ($p->fungsionalTugas as $ft)
                                                {{-- Coret teks jika status non-aktif --}}
                                                <span
                                                    class="{{ $ft->pivot->status == 'non_aktif' ? 'text-decoration-line-through text-muted' : '' }}">
                                                    {{ $ft->tugas }}
                                                </span>
                                                {{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </div>

                                    {{-- Wilayah/Daerah --}}
                                    <div class="small text-muted text-truncate">
                                        <i data-feather="map-pin" style="width: 12px;" class="me-1"></i>
                                        {{ $p->daerah->nama_daerah ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            {{-- TOMBOL DETAIL --}}
                            <div class="action-area">
                                <a href="{{ route('pokok.pengurus.show', $p->id) }}"
                                    class="btn btn-sm btn-outline-success w-100">
                                    Lihat Detail <i data-feather="arrow-right" style="width: 14px;" class="ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center py-5">
                            <i data-feather="users" style="width: 48px; height: 48px; color: #adb5bd;"></i>
                            <p class="text-muted mt-3 mb-0">Belum ada data Wali Asuh ditemukan.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
@endsection

@section('this-page-scripts')
    {{-- Script Icons --}}
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') feather.replace();
        });
    </script>
@endsection
