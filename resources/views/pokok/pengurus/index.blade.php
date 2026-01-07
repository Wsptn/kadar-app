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

        /* Spinner loading saat auto submit */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
        }

        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .filter-select {
            min-width: 140px;
            max-width: 200px;
        }
    </style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Data Pengurus</h2>
            </div>
        </div>
        <div class="bg-light p-2 mb-3 border rounded small">
            <span>Data Pokok / <span class="text-success fw-semibold">Pengurus</span></span>
        </div>

        {{-- CARD FILTER --}}
        <div class="card shadow-sm mb-4 border-0 position-relative">

            {{-- Loading Spinner --}}
            <div id="filterLoading" class="loading-overlay d-none">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div class="card-body py-3">
                <form method="GET" action="{{ route('pokok.pengurus.index') }}" id="filterForm">
                    <div class="d-flex flex-wrap gap-2 align-items-center">

                        {{-- 1. WILAYAH --}}
                        @if (Auth::user()->isAdmin() || Auth::user()->isBiktren())
                            <select name="wilayah" id="wilayahSelect" class="form-select filter-select auto-submit">
                                <option value="">-- Wilayah --</option>
                                @foreach ($wilayahList as $w)
                                    <option value="{{ $w->id }}"
                                        {{ request('wilayah') == $w->id ? 'selected' : '' }}>
                                        {{ $w->nama_wilayah }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        {{-- 2. DAERAH --}}
                        @if (!Auth::user()->isDaerah())
                            <select name="daerah" id="daerahSelect" class="form-select filter-select auto-submit"
                                {{ (Auth::user()->isAdmin() || Auth::user()->isBiktren()) && empty(request('wilayah')) ? 'disabled' : '' }}>
                                <option value="">-- Daerah --</option>
                                @foreach ($daerahList as $d)
                                    <option value="{{ $d->id }}" {{ request('daerah') == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama_daerah }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        {{-- 3. ENTITAS DAERAH --}}
                        <select name="entitas_daerah" class="form-select filter-select auto-submit">
                            <option value="">-- Entitas Daerah --</option>
                            @php
                                $opsiEntitas = [
                                    'LPBA',
                                    'Idadiyah SLTP',
                                    'Teknologi',
                                    'BPK & Awwaliyah',
                                    'Pondok Mahasiswa (POMAS)',
                                    'SPThree (KIP)',
                                    'Bahasa',
                                    'MINM',
                                    'LIPS',
                                    'MAK',
                                    'MIPA SMP & SMA',
                                    'MIPA MANJ',
                                    'Diniyah',
                                    'Haddamiyah',
                                    'IPS',
                                    'Tahsin (PPIQ)',
                                    'Tahfidz (PPIQ)',
                                    'Awwaliyah',
                                    'Idadiyah SLTA',
                                ];
                            @endphp
                            @foreach ($opsiEntitas as $op)
                                <option value="{{ $op }}"
                                    {{ request('entitas_daerah') == $op ? 'selected' : '' }}>
                                    {{ $op }}
                                </option>
                            @endforeach
                        </select>

                        {{-- === FILTER BERTINGKAT (ENTITAS -> JABATAN) === --}}

                        {{-- 4. ENTITAS (Pemicu Utama) --}}
                        <select name="entitas" id="entitasFilter" class="form-select filter-select">
                            <option value="">-- Entitas --</option>
                            @foreach ($entitasList as $e)
                                <option value="{{ $e->id }}" {{ request('entitas') == $e->id ? 'selected' : '' }}>
                                    {{ $e->nama_entitas }}
                                </option>
                            @endforeach
                        </select>

                        {{-- 5. JABATAN (Disabled Awal, diisi JS) --}}
                        {{-- Ditambahkan class 'auto-submit' agar form tersubmit saat jabatan dipilih --}}
                        <select name="jabatan" id="jabatanFilter" class="form-select filter-select auto-submit" disabled>
                            <option value="">-- Pilih Entitas Dulu --</option>
                        </select>


                        {{-- ============================================================== --}}

                        {{-- 6. FUNGSIONAL TUGAS --}}
                        <select name="tugas" class="form-select filter-select auto-submit">
                            <option value="">-- Fungsional --</option>
                            @foreach ($fungsionalList as $ft)
                                <option value="{{ $ft->id_tugas }}"
                                    {{ request('tugas') == $ft->id_tugas ? 'selected' : '' }}>
                                    {{ $ft->tugas }}
                                </option>
                            @endforeach
                        </select>

                        {{-- 7. TUGAS INTERNAL --}}
                        <select name="internal" class="form-select filter-select auto-submit">
                            <option value="">-- Internal --</option>
                            @foreach ($internalList as $ti)
                                <option value="{{ $ti->id_internal }}"
                                    {{ request('internal') == $ti->id_internal ? 'selected' : '' }}>
                                    {{ $ti->internal }}
                                </option>
                            @endforeach
                        </select>

                        {{-- 8. TUGAS EKSTERNAL --}}
                        <select name="eksternal" class="form-select filter-select auto-submit">
                            <option value="">-- Eksternal --</option>
                            @foreach ($eksternalList as $te)
                                <option value="{{ $te->id_eksternal }}"
                                    {{ request('eksternal') == $te->id_eksternal ? 'selected' : '' }}>
                                    {{ $te->eksternal }}
                                </option>
                            @endforeach
                        </select>

                        {{-- 9. PENDIDIKAN --}}
                        <select name="pendidikan" class="form-select filter-select auto-submit">
                            <option value="">-- Pendidikan --</option>
                            @foreach ($pendidikanList as $p)
                                <option value="{{ $p->id_pendidikan }}"
                                    {{ request('pendidikan') == $p->id_pendidikan ? 'selected' : '' }}>
                                    {{ $p->nama_pendidikan }}
                                </option>
                            @endforeach
                        </select>

                        {{-- 10. ANGKATAN --}}
                        <select name="angkatan" class="form-select filter-select auto-submit">
                            <option value="">-- Angkatan --</option>
                            @foreach ($angkatanList as $a)
                                <option value="{{ $a->id_angkatan }}"
                                    {{ request('angkatan') == $a->id_angkatan ? 'selected' : '' }}>
                                    {{ $a->angkatan }}
                                </option>
                            @endforeach
                        </select>

                        {{-- 11. STATUS --}}
                        <select name="status" class="form-select filter-select auto-submit">
                            <option value="">-- Status --</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="non_aktif" {{ request('status') === 'non_aktif' ? 'selected' : '' }}>
                                Non Aktif
                            </option>
                        </select>

                    </div>

                    {{-- SEARCH & RESET --}}
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <div class="input-group" style="width: 200px; flex-shrink: 0;">
                            <span class="input-group-text bg-white border-end-0">
                                <i data-feather="search" style="width: 16px;"></i>
                            </span>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                class="form-control border-start-0 ps-0" placeholder="Cari Nama / NIUP..."
                                autocomplete="off">
                        </div>
                        <a href="{{ route('pokok.pengurus.index') }}"
                            class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
                            title="Reset Filter" style="width: 32px; height: 30px; padding: 0;">
                            <i data-feather="refresh-cw" style="width: 16px;"></i>
                        </a>
                        {{-- Tombol Filter Manual (Optional) --}}
                        <button type="submit" class="btn btn-primary d-none">Cari</button>
                    </div>

                </form>
            </div>
        </div>

        {{-- INFO BAR --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div class="d-flex gap-2">
                @if (!Auth::user()->isDaerah())
                    <a href="{{ route('pokok.pengurus.create') }}"
                        class="btn btn-success d-flex align-items-center shadow-sm">
                        <i data-feather="plus" class="me-1"></i> Tambah Pengurus
                    </a>
                @endif
                <a href="{{ route('pokok.pengurus.export', request()->all()) }}"
                    class="btn btn-success d-flex align-items-center shadow-sm">
                    <i data-feather="file-text" class="me-1"></i> Excel
                </a>
            </div>
            <div class="text-muted small">
                Menampilkan <strong>{{ $pengurus->count() }}</strong> dari total <strong>{{ $pengurus->total() }}</strong>
                data
                @if (request('search'))
                    hasil pencarian "<strong>{{ request('search') }}</strong>"
                @endif
            </div>
        </div>

        {{-- GRID DATA --}}
        @if ($pengurus->count() > 0)
            <div class="row">
                @foreach ($pengurus as $p)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card shadow-sm pengurus-card h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div class="pengurus-photo-wrapper me-3">
                                        <img src="{{ $p->foto ? asset('storage/' . $p->foto) : asset('template-admin/img/default-avatar.png') }}"
                                            class="pengurus-photo" alt="{{ $p->nama }}"
                                            onerror="this.onerror=null; this.src='{{ asset('template-admin/img/default-avatar.png') }}'">
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="fw-bold mb-1 text-truncate" title="{{ $p->nama }}">
                                            {{ $p->nama }}</h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-light text-dark border me-2">{{ $p->niup }}</span>
                                            @if ($p->status == 'aktif')
                                                <span class="badge bg-success" style="font-size: 0.65rem;">Aktif</span>
                                            @else
                                                <span class="badge bg-danger" style="font-size: 0.65rem;">Non-Aktif</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted mb-1">
                                            <i data-feather="briefcase" style="width: 12px;" class="me-1"></i>
                                            {{ $p->jabatan->nama_jabatan ?? '-' }}
                                        </div>
                                        <div class="small text-muted">
                                            <i data-feather="map-pin" style="width: 12px;" class="me-1"></i>
                                            {{ $p->daerah->nama_daerah ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="action-area mt-3 pt-2 border-top">
                                    <a href="{{ route('pokok.pengurus.show', $p->id) }}"
                                        class="btn btn-sm btn-outline-success w-100">
                                        Lihat Detail <i data-feather="arrow-right" style="width: 14px;"
                                            class="ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- PAGINATION LINKS --}}
            <div class="d-flex justify-content-end mt-4">
                @if ($pengurus->hasPages())
                    {{ $pengurus->links('pagination::bootstrap-5') }}
                @endif
            </div>
        @else
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i data-feather="users" style="width: 48px; height: 48px; color: #adb5bd;"></i>
                    <p class="text-muted mt-3 mb-0">Tidak ada data ditemukan.</p>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('this-page-scripts')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') feather.replace();

            const form = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const loadingOverlay = document.getElementById('filterLoading');
            let typingTimer;

            // === FUNGSI SUBMIT FORM ===
            function submitForm() {
                loadingOverlay.classList.remove('d-none');
                form.submit();
            }

            // === 1. LOGIKA AUTO SUBMIT (Untuk filter Non-Bertingkat) ===
            const autoSubmits = document.querySelectorAll('.auto-submit');
            autoSubmits.forEach(select => {
                select.addEventListener('change', submitForm);
            });

            // === 2. LOGIKA SEARCH (Delay Submit) ===
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(typingTimer);
                    const val = this.value;
                    if (val.length === 0 || val.length >= 3) {
                        typingTimer = setTimeout(submitForm, 800);
                    }
                });
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        submitForm();
                    }
                });
            }

            // === 3. LOGIKA DEPENDENT DROPDOWN (AJAX) ===

            // Elemen
            const entitasFilter = document.getElementById('entitasFilter');
            const jabatanFilter = document.getElementById('jabatanFilter');

            // Data Lama (Old Values) dari URL
            const oldEntitas = "{{ request('entitas') }}";
            const oldJabatan = "{{ request('jabatan') }}";

            // Helper: Fetch Data
            async function fetchOptions(url, targetEl, placeholder, selectedId = null) {
                targetEl.innerHTML = '<option>Memuat...</option>';
                targetEl.disabled = true;

                try {
                    const response = await fetch(url);
                    const data = await response.json();

                    let html = `<option value="">${placeholder}</option>`;
                    data.forEach(item => {
                        let id = item.id;
                        let name = item.nama_jabatan;
                        let isSelected = (selectedId == id) ? 'selected' : '';
                        html += `<option value="${id}" ${isSelected}>${name}</option>`;
                    });

                    targetEl.innerHTML = html;
                    targetEl.disabled = false;

                } catch (error) {
                    console.error('Error fetching data:', error);
                    targetEl.innerHTML = '<option value="">Gagal</option>';
                }
            }

            // A. Event Change ENTITAS
            entitasFilter.addEventListener('change', function() {
                const id = this.value;

                // Reset anak-anaknya
                jabatanFilter.innerHTML = '<option value="">-- Pilih Entitas Dulu --</option>';
                jabatanFilter.disabled = true;

                if (id) {
                    fetchOptions(`/master/jabatan/get-jabatan/${id}`, jabatanFilter, '-- Jabatan --');
                }
            });

            // === 4. INISIALISASI DATA (PERSISTENCE) ===
            // Agar saat page reload, dropdown bertingkat tidak reset.
            if (oldEntitas) {
                // 1. Load Jabatan berdasarkan Entitas yg terpilih
                fetchOptions(`/master/jabatan/get-jabatan/${oldEntitas}`, jabatanFilter, '-- Jabatan --',
                    oldJabatan);
            }

        });
    </script>
@endsection
