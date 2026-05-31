@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Pengurus</h1>
            <a href="{{ route('pokok.pengurus.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Proteksi Tambahan di View --}}
        @if (Auth::user()->isDaerah())
            <div class="alert alert-danger">
                Akun Daerah tidak diizinkan menambah data pengurus.
            </div>
        @else
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 fw-bold">Form Tambah Pengurus</h5>
                </div>
                <div class="card-body">

                    {{-- Tampilkan Error Validasi Global --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pokok.pengurus.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- NIUP --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIUP <span class="text-danger">*</span></label>
                            <input type="text" name="niup" value="{{ old('niup') }}"
                                class="form-control @error('niup') is-invalid @enderror" required>
                            @error('niup')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama') }}"
                                class="form-control @error('nama') is-invalid @enderror" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3 fw-bold text-success">Lokasi Tugas</h6>

                        {{-- Wilayah (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Wilayah <span class="text-danger">*</span></label>

                            @if (Auth::user()->isWilayah())
                                {{-- User Wilayah: Otomatis Terisi --}}
                                <input type="hidden" id="wilayahSelect" name="wilayah"
                                    value="{{ Auth::user()->wilayah }}">
                                <input type="text" class="form-control bg-light"
                                    value="{{ Auth::user()->wilayah ?? '-' }}" readonly>
                            @else
                                {{-- Admin/Biktren: Wajib Pilih --}}
                                <select id="wilayahSelect" name="wilayah"
                                    class="form-select @error('wilayah') is-invalid @enderror" required>
                                    <option value="">-- Pilih Wilayah --</option>
                                    @foreach ($wilayahs as $w)
                                        <option value="{{ $w }}"
                                            {{ old('wilayah') == $w ? 'selected' : '' }}>
                                            {{ $w }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('wilayah')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Daerah (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Daerah <span class="text-danger">*</span></label>
                            <select id="daerahSelect" name="daerah"
                                class="form-select @error('daerah') is-invalid @enderror" disabled required>
                                <option value="">-- Pilih Wilayah Terlebih Dahulu --</option>
                            </select>
                            @error('daerah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- === ENTITAS DAERAH (DYNAMIC) === --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Entitas Daerah</label>

                            <select name="entitas_daerah" class="form-select @error('entitas_daerah') is-invalid @enderror">
                                <option value="">-- Pilih Entitas Daerah (Opsional) --</option>

                                @foreach ($entitasDaerahs as $ed)
                                    <option value="{{ $ed }}" {{ old('entitas_daerah') == $ed ? 'selected' : '' }}>
                                        {{ $ed }}
                                    </option>
                                @endforeach
                            </select>

                            @error('entitas_daerah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- =================================================== --}}

                        {{-- Kamar --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kamar <span class="text-danger">*</span></label>

                            <select id="kamarSelect" name="domisili_id"
                                class="form-select @error('domisili_id') is-invalid @enderror" disabled required>

                                <option value="">-- Pilih Daerah Terlebih Dahulu --</option>
                            </select>

                            @error('domisili_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3 fw-bold text-success">Kelembagaan</h6>

                        {{-- Entitas (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Entitas <span class="text-danger">*</span></label>
                            <select id="entitasSelect" class="form-select" required>
                                <option value="">-- Pilih Entitas --</option>
                                @foreach ($entitasList as $e)
                                    <option value="{{ $e->entitas }}">{{ $e->entitas }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jabatan (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                            <select id="jabatanSelect" class="form-select" disabled required>
                                <option value="">-- Pilih Entitas Terlebih Dahulu --</option>
                            </select>
                        </div>

                        {{-- Jenis Jabatan (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Jabatan <span class="text-danger">*</span></label>
                            <select id="jenisSelect" class="form-select" disabled required>
                                <option value="">-- Pilih Jabatan Terlebih Dahulu --</option>
                            </select>
                        </div>

                        {{-- Grade Jabatan (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Grade Jabatan <span class="text-danger">*</span></label>
                            <select id="gradeSelect" name="struktur_jabatan_id"
                                class="form-select @error('struktur_jabatan_id') is-invalid @enderror" disabled required>
                                <option value="">-- Pilih Jenis Jabatan Terlebih Dahulu --</option>
                            </select>
                            @error('struktur_jabatan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SK Kepengurusan --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">No. SK Kepengurusan</label>
                            <input type="text" name="sk_kepengurusan" value="{{ old('sk_kepengurusan') }}"
                                class="form-control" placeholder="Contoh: SK/2024/001">
                        </div>

                        {{-- Tanggal Mulai Jabatan --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Mulai Jabatan Struktural <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_mulai_jabatan" value="{{ old('tgl_mulai_jabatan', date('Y-m-d')) }}"
                                class="form-control" required>
                        </div>
                        <hr class="my-4">
                        <h6 class="mb-3 fw-bold text-success">Data Pendukung</h6>

                        {{-- Fungsional Tugas --}}
                        {{-- Fungsional Tugas (MULTI SELECT) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fungsional Tugas (Bisa pilih lebih dari satu)</label>
                            <div class="card p-3 bg-light border">
                                @foreach ($fungsionalTugas as $index => $ft)
                                    <div class="row align-items-center mb-2 pb-2 border-bottom">
                                        <div class="col-md-5">
                                            <div class="form-check">
                                                <input class="form-check-input tugas-checkbox" type="checkbox"
                                                    name="tugas[{{ $ft->id_tugas }}][id]" value="{{ $ft->id_tugas }}"
                                                    id="tugas_{{ $ft->id_tugas }}"
                                                    data-index="{{ $ft->id_tugas }}">
                                                <label class="form-check-label fw-bold" for="tugas_{{ $ft->id_tugas }}">
                                                    {{ $ft->nama_tugas }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light text-muted" title="Tanggal Mulai Tugas"><i class="bi bi-calendar"></i></span>
                                                <input type="date" name="tugas[{{ $ft->id_tugas }}][tgl_mulai]" 
                                                    class="form-control" id="tgl_tugas_{{ $ft->id_tugas }}" 
                                                    value="{{ date('Y-m-d') }}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="tugas[{{ $ft->id_tugas }}][status]"
                                                class="form-select form-select-sm status-select"
                                                id="status_{{ $ft->id_tugas }}" disabled>
                                                <option value="aktif">Aktif</option>
                                                <option value="non_aktif">Non Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Tugas Internal (SINGLE SELECT) --}}
                        <div class="row mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Tugas Internal</label>
                                <select name="tugas_internal_id" class="form-select">
                                    <option value="">-- Tidak Ada --</option>
                                    @foreach ($rangkapInternals as $ri)
                                        <option value="{{ $ri->id_tugas }}">{{ $ri->nama_tugas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Tgl Mulai Internal</label>
                                <input type="date" name="tgl_mulai_tugas_internal" value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                        </div>

                        {{-- Tugas Eksternal (SINGLE SELECT) --}}
                        <div class="row mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Tugas Eksternal</label>
                                <select name="tugas_eksternal_id" class="form-select">
                                    <option value="">-- Tidak Ada --</option>
                                    @foreach ($rangkapEksternals as $re)
                                        <option value="{{ $re->id_tugas }}">{{ $re->nama_tugas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Tgl Mulai Eksternal</label>
                                <input type="date" name="tgl_mulai_tugas_eksternal" value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                        </div>
                        
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const checkboxes = document.querySelectorAll('.tugas-checkbox');
                                checkboxes.forEach(chk => {
                                    chk.addEventListener('change', function() {
                                        const idx = this.getAttribute('data-index');
                                        const select = document.getElementById(`status_${idx}`);
                                        const tglInput = document.getElementById(`tgl_tugas_${idx}`);
                                        if (this.checked) {
                                            select.removeAttribute('disabled');
                                            tglInput.removeAttribute('disabled');
                                            tglInput.setAttribute('required', 'required');
                                        } else {
                                            select.setAttribute('disabled', true);
                                            tglInput.setAttribute('disabled', true);
                                            tglInput.removeAttribute('required');
                                        }
                                    });
                                });
                            });
                        </script>

                        {{-- Pendidikan & Angkatan --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Pendidikan</label>
                                <select name="pendidikan_id" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($pendidikans as $pd)
                                        <option value="{{ $pd->id_pendidikan }}"
                                            {{ old('pendidikan_id') == $pd->id_pendidikan ? 'selected' : '' }}>
                                            {{ $pd->nama_pendidikan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Angkatan</label>
                                <select name="angkatan_id" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($angkatans as $ag)
                                        <option value="{{ $ag->id_angkatan }}"
                                            {{ old('angkatan_id') == $ag->id_angkatan ? 'selected' : '' }}>
                                            {{ $ag->angkatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3 fw-bold text-success">Berkas & Foto</h6>

                        {{-- Foto Profil --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Foto Profil (jpg/png)</label>

                            <input type="file" name="foto" id="foto"
                                accept="image/jpeg, image/png, image/jpg"
                                class="form-control @error('foto') is-invalid @enderror">

                            <small class="text-muted d-block mt-1">
                                <i data-feather="info" style="width: 14px; margin-top: -2px;"></i>
                                Format: JPG, JPEG, PNG | Maks: 15 MB | Resolusi Min: 1080 x 1080 px
                            </small>

                            @error('foto')
                                <div class="invalid-feedback fw-bold d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Upload Berkas (Hanya Admin/Biktren) --}}
                        @if (Auth::user()->isAdmin() || Auth::user()->isBiktren())
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Berkas SK Pengurus</label>
                                    <input type="file" name="berkas_sk_pengurus" class="form-control">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Berkas Surat Tugas</label>
                                    <input type="file" name="berkas_surat_tugas" class="form-control">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Berkas PLT</label>
                                    <input type="file" name="berkas_plt" class="form-control">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Berkas Lain</label>
                                    <input type="file" name="berkas_lain" class="form-control">
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning d-flex align-items-center mt-2" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>
                                    <strong>Perhatian:</strong> Akun Wilayah hanya diizinkan mengunggah Foto Profil.
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end pt-2">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-save me-1"></i> Simpan Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('page-scripts')
    {{-- Script JS tetap sama (tidak berubah) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            // Function Load Daerah
            function loadDaerah(wilayahId, selectedDaerahId = null) {
                if (!wilayahId) {
                    $('#daerahSelect').prop('disabled', true).html(
                        '<option value="">-- Pilih Wilayah Terlebih Dahulu --</option>');
                    return;
                }

                $('#daerahSelect').prop('disabled', true).html('<option value="">Memuat...</option>');
                $('#kamarSelect').prop('disabled', true).html(
                    '<option value="">-- Pilih Daerah Terlebih Dahulu --</option>');

                $.get(`/master/domisili/get-daerah/${encodeURIComponent(wilayahId)}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Daerah --</option>';
                        data.forEach(d => {
                            let isSelected = (selectedDaerahId == d.daerah) ? 'selected' : '';
                            html += `<option value="${d.daerah}" ${isSelected}>${d.daerah}</option>`;
                        });
                        $('#daerahSelect').html(html).prop('disabled', false);
                    })
                    .fail(function() {
                        $('#daerahSelect').html('<option value="">Gagal memuat data</option>');
                    });
            }

            // Event Wilayah Change
            $('#wilayahSelect').on('change', function() {
                loadDaerah($(this).val());
            });

            // Auto Run (User Wilayah)
            let initialWilayahId = $('#wilayahSelect').val();
            let oldDaerahId = "{{ old('daerah') }}";
            if (initialWilayahId) {
                loadDaerah(initialWilayahId, oldDaerahId);
            }

            // Daerah -> Kamar
            $('#daerahSelect').on('change', function() {
                const daerahId = $(this).val();
                const wilayahId = $('#wilayahSelect').val();
                $('#kamarSelect').prop('disabled', true).html('<option value="">Memuat...</option>');

                if (!daerahId || !wilayahId) {
                    $('#kamarSelect').prop('disabled', true).html(
                        '<option value="">-- Pilih Daerah Terlebih Dahulu --</option>');
                    return;
                }

                $.get(`/master/domisili/get-kamar/${encodeURIComponent(wilayahId)}/${encodeURIComponent(daerahId)}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Kamar --</option>';
                        data.forEach(k => html +=
                            `<option value="${k.id}">${k.kamar}</option>`);
                        $('#kamarSelect').html(html).prop('disabled', false);
                    });
            });

            // Entitas -> Jabatan
            $('#entitasSelect').on('change', function() {
                const entitas = $(this).val();
                $('#jabatanSelect').prop('disabled', true).html('<option value="">Memuat...</option>');
                $('#jenisSelect').prop('disabled', true).html('<option value="">-- Pilih Jabatan Terlebih Dahulu --</option>');
                $('#gradeSelect').prop('disabled', true).html('<option value="">-- Pilih Jenis Jabatan Terlebih Dahulu --</option>');

                if (!entitas) {
                    $('#jabatanSelect').prop('disabled', true).html('<option value="">-- Pilih Entitas Terlebih Dahulu --</option>');
                    return;
                }

                $.get(`/master/struktur_jabatan/get-jabatan/${encodeURIComponent(entitas)}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Jabatan --</option>';
                        data.forEach(j => html += `<option value="${j.jabatan}">${j.jabatan}</option>`);
                        $('#jabatanSelect').html(html).prop('disabled', false);
                    });
            });

            // Jabatan -> Jenis
            $('#jabatanSelect').on('change', function() {
                const entitas = $('#entitasSelect').val();
                const jabatan = $(this).val();
                $('#jenisSelect').prop('disabled', true).html('<option value="">Memuat...</option>');
                $('#gradeSelect').prop('disabled', true).html('<option value="">-- Pilih Jenis Jabatan Terlebih Dahulu --</option>');

                if (!jabatan) {
                    $('#jenisSelect').prop('disabled', true).html('<option value="">-- Pilih Jabatan Terlebih Dahulu --</option>');
                    return;
                }

                $.get(`/master/struktur_jabatan/get-jenis/${encodeURIComponent(entitas)}/${encodeURIComponent(jabatan)}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Jenis Jabatan --</option>';
                        data.forEach(j => html += `<option value="${j.jenis_jabatan}">${j.jenis_jabatan}</option>`);
                        $('#jenisSelect').html(html).prop('disabled', false);
                    });
            });

            // Jenis -> Grade
            $('#jenisSelect').on('change', function() {
                const entitas = $('#entitasSelect').val();
                const jabatan = $('#jabatanSelect').val();
                const jenis = $(this).val();
                $('#gradeSelect').prop('disabled', true).html('<option value="">Memuat...</option>');

                if (!jenis) {
                    $('#gradeSelect').prop('disabled', true).html('<option value="">-- Pilih Jenis Jabatan Terlebih Dahulu --</option>');
                    return;
                }

                $.get(`/master/struktur_jabatan/get-grade/${encodeURIComponent(entitas)}/${encodeURIComponent(jabatan)}/${encodeURIComponent(jenis)}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Grade --</option>';
                        data.forEach(g => html += `<option value="${g.id}">${g.grade}</option>`);
                        $('#gradeSelect').html(html).prop('disabled', false);
                    });
            });
        });
    </script>
@endpush
