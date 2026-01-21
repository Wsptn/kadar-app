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
                                <input type="hidden" id="wilayahSelect" name="wilayah_id"
                                    value="{{ Auth::user()->wilayah_id }}">
                                <input type="text" class="form-control bg-light"
                                    value="{{ Auth::user()->wilayah->nama_wilayah ?? '-' }}" readonly>
                            @else
                                {{-- Admin/Biktren: Wajib Pilih --}}
                                <select id="wilayahSelect" name="wilayah_id"
                                    class="form-select @error('wilayah_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Wilayah --</option>
                                    @foreach ($wilayahs as $w)
                                        <option value="{{ $w->id }}"
                                            {{ old('wilayah_id') == $w->id ? 'selected' : '' }}>
                                            {{ $w->nama_wilayah }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('wilayah_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Daerah (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Daerah <span class="text-danger">*</span></label>
                            <select id="daerahSelect" name="daerah_id"
                                class="form-select @error('daerah_id') is-invalid @enderror" disabled required>
                                <option value="">-- Pilih Wilayah Terlebih Dahulu --</option>
                            </select>
                            @error('daerah_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- === ENTITAS DAERAH (MANUAL LIST) === --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Entitas Daerah</label>

                            <select name="entitas_daerah" class="form-select @error('entitas_daerah') is-invalid @enderror">
                                <option value="">-- Pilih Entitas Daerah (Opsional) --</option>

                                <option value="LPBA" {{ old('entitas_daerah') == 'LPBA' ? 'selected' : '' }}>LPBA
                                </option>
                                <option value="Idadiyah SLTP"
                                    {{ old('entitas_daerah') == 'Idadiyah SLTP' ? 'selected' : '' }}>Idadiyah SLTP
                                </option>
                                <option value="Teknologi" {{ old('entitas_daerah') == 'Teknologi' ? 'selected' : '' }}>
                                    Teknologi
                                </option>
                                <option value="BPK & Awwaliyah"
                                    {{ old('entitas_daerah') == 'BPK & Awwaliyah' ? 'selected' : '' }}>BPK & Awwaliyah
                                </option>
                                <option value="Pondok Mahasiswa (POMAS)"
                                    {{ old('entitas_daerah') == 'Pondok Mahasiswa (POMAS)' ? 'selected' : '' }}>Pondok
                                    Mahasiswa (POMAS)
                                </option>
                                <option value="SPThree (KIP)"
                                    {{ old('entitas_daerah') == 'SPThree (KIP)' ? 'selected' : '' }}>SPThree (KIP)
                                </option>
                                <option value="Bahasa" {{ old('entitas_daerah') == 'Bahasa' ? 'selected' : '' }}>Bahasa
                                </option>
                                <option value="MINM" {{ old('entitas_daerah') == 'MINM' ? 'selected' : '' }}>MINM
                                </option>
                                <option value="LIPS" {{ old('entitas_daerah') == 'LIPS' ? 'selected' : '' }}>LIPS
                                </option>
                                <option value="MAK" {{ old('entitas_daerah') == 'MAK' ? 'selected' : '' }}>MAK
                                </option>
                                <option value="MIPA SMP & SMA"
                                    {{ old('entitas_daerah') == 'MIPA SMP & SMA' ? 'selected' : '' }}>MIPA SMP & SMA
                                </option>
                                <option value="MIPA MANJ" {{ old('entitas_daerah') == 'MIPA MANJ' ? 'selected' : '' }}>MIPA
                                    MANJ
                                </option>
                                <option value="Diniyah" {{ old('entitas_daerah') == 'Diniyah' ? 'selected' : '' }}>Diniyah
                                </option>
                                <option value="Haddamiyah" {{ old('entitas_daerah') == 'Haddamiyah' ? 'selected' : '' }}>
                                    Haddamiyah
                                </option>
                                <option value="IPS" {{ old('entitas_daerah') == 'IPS' ? 'selected' : '' }}>IPS
                                </option>
                                <option value="Tahsin (PPIQ)"
                                    {{ old('entitas_daerah') == 'Tahsin (PPIQ)' ? 'selected' : '' }}>Tahsin (PPIQ)
                                </option>
                                <option value="Tahfidz (PPIQ)"
                                    {{ old('entitas_daerah') == 'Tahfidz (PPIQ)' ? 'selected' : '' }}>Tahfidz (PPIQ)
                                </option>
                                <option value="Awwaliyah" {{ old('entitas_daerah') == 'Awwaliyah' ? 'selected' : '' }}>
                                    Awwaliyah
                                </option>
                                <option value="Idadiyah SLTA"
                                    {{ old('entitas_daerah') == 'Idadiyah SLTA' ? 'selected' : '' }}>Idadiyah SLTA
                                </option>
                                {{-- Tambahkan opsi lain jika perlu --}}
                            </select>

                            @error('entitas_daerah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- =================================================== --}}

                        {{-- Kamar --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kamar <span class="text-danger">*</span></label>

                            <select id="kamarSelect" name="kamar_id"
                                class="form-select @error('kamar_id') is-invalid @enderror" disabled required>

                                <option value="">-- Pilih Daerah Terlebih Dahulu --</option>
                            </select>

                            @error('kamar_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3 fw-bold text-success">Kelembagaan</h6>

                        {{-- Entitas (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Entitas <span class="text-danger">*</span></label>
                            <select id="entitasSelect" name="entitas_id"
                                class="form-select @error('entitas_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Entitas --</option>
                                @foreach ($entitas as $e)
                                    <option value="{{ $e->id }}"
                                        {{ old('entitas_id') == $e->id ? 'selected' : '' }}>
                                        {{ $e->nama_entitas }}</option>
                                @endforeach
                            </select>
                            @error('entitas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jabatan (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                            <select id="jabatanSelect" name="jabatan_id"
                                class="form-select @error('jabatan_id') is-invalid @enderror" disabled required>
                                <option value="">-- Pilih Entitas Terlebih Dahulu --</option>
                            </select>
                            @error('jabatan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jenis Jabatan (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Jabatan <span class="text-danger">*</span></label>
                            <select id="jenisSelect" name="jenis_jabatan_id"
                                class="form-select @error('jenis_jabatan_id') is-invalid @enderror" disabled required>
                                <option value="">-- Pilih Jabatan Terlebih Dahulu --</option>
                            </select>
                            @error('jenis_jabatan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Grade Jabatan (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Grade Jabatan <span class="text-danger">*</span></label>
                            <select id="gradeSelect" name="grade_jabatan_id"
                                class="form-select @error('grade_jabatan_id') is-invalid @enderror" disabled required>
                                <option value="">-- Pilih Jenis Jabatan Terlebih Dahulu --</option>
                            </select>
                            @error('grade_jabatan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SK Kepengurusan --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">No. SK Kepengurusan</label>
                            <input type="text" name="sk_kepengurusan" value="{{ old('sk_kepengurusan') }}"
                                class="form-control" placeholder="Contoh: SK/2024/001">
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
                                        {{-- CHECKBOX NAMA TUGAS --}}
                                        <div class="col-md-7">
                                            <div class="form-check">
                                                {{-- ID Tugas dikirim sebagai array tugas[index][id] --}}
                                                <input class="form-check-input tugas-checkbox" type="checkbox"
                                                    name="tugas[{{ $index }}][id]" value="{{ $ft->id_tugas }}"
                                                    {{-- Pastikan ini ID primary key tabel master tugas --}} id="tugas_{{ $index }}"
                                                    data-index="{{ $index }}">
                                                <label class="form-check-label fw-bold" for="tugas_{{ $index }}">
                                                    {{ $ft->tugas }}
                                                </label>
                                            </div>
                                        </div>

                                        {{-- PILIHAN STATUS (Aktif/Non) --}}
                                        <div class="col-md-5">
                                            <select name="tugas[{{ $index }}][status]"
                                                class="form-select form-select-sm status-select"
                                                id="status_{{ $index }}" disabled>
                                                <option value="aktif">Aktif</option>
                                                <option value="non_aktif">Non Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted fst-italic">*Centang tugas untuk mengaktifkan pilihan status.</small>
                        </div>

                        {{-- Script Khusus Checkbox (Taruh langsung di bawah div ini atau di section scripts) --}}
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const checkboxes = document.querySelectorAll('.tugas-checkbox');
                                checkboxes.forEach(chk => {
                                    chk.addEventListener('change', function() {
                                        const idx = this.getAttribute('data-index');
                                        const select = document.getElementById(`status_${idx}`);
                                        if (this.checked) {
                                            select.removeAttribute('disabled');
                                        } else {
                                            select.setAttribute('disabled', true);
                                        }
                                    });
                                });
                            });
                        </script>

                        {{-- Rangkap Tugas --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Rangkap Tugas Internal</label>
                                <select name="rangkap_internal_id" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($rangkapInternals as $ri)
                                        <option value="{{ $ri->id_internal }}"
                                            {{ old('rangkap_internal_id') == $ri->id_internal ? 'selected' : '' }}>
                                            {{ $ri->internal }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Rangkap Tugas Eksternal</label>
                                <select name="rangkap_eksternal_id" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($rangkapEksternals as $re)
                                        <option value="{{ $re->id_eksternal }}"
                                            {{ old('rangkap_eksternal_id') == $re->id_eksternal ? 'selected' : '' }}>
                                            {{ $re->eksternal }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

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
                            <input type="file" name="foto"
                                class="form-control @error('foto') is-invalid @enderror">
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: jpg, jpeg, png. Maks: 4MB.</small>
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

                $.get(`/master/domisili/get-daerah/${wilayahId}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Daerah --</option>';
                        data.forEach(d => {
                            let isSelected = (selectedDaerahId == d.id) ? 'selected' : '';
                            html += `<option value="${d.id}" ${isSelected}>${d.nama_daerah}</option>`;
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
            let oldDaerahId = "{{ old('daerah_id') }}";
            if (initialWilayahId) {
                loadDaerah(initialWilayahId, oldDaerahId);
            }

            // Daerah -> Kamar
            $('#daerahSelect').on('change', function() {
                const daerahId = $(this).val();
                $('#kamarSelect').prop('disabled', true).html('<option value="">Memuat...</option>');

                if (!daerahId) {
                    $('#kamarSelect').prop('disabled', true).html(
                        '<option value="">-- Pilih Daerah Terlebih Dahulu --</option>');
                    return;
                }

                $.get(`/master/domisili/get-kamar/${daerahId}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Kamar --</option>';
                        data.forEach(k => html +=
                            `<option value="${k.id}">${k.nomor_kamar}</option>`);
                        $('#kamarSelect').html(html).prop('disabled', false);
                    });
            });

            // Entitas -> Jabatan
            $('#entitasSelect').on('change', function() {
                const entitasId = $(this).val();
                $('#jabatanSelect').prop('disabled', true).html('<option value="">Memuat...</option>');
                $('#jenisSelect').prop('disabled', true).html(
                    '<option value="">-- Pilih Jabatan Terlebih Dahulu --</option>');
                $('#gradeSelect').prop('disabled', true).html(
                    '<option value="">-- Pilih Jenis Jabatan Terlebih Dahulu --</option>');

                if (!entitasId) {
                    $('#jabatanSelect').prop('disabled', true).html(
                        '<option value="">-- Pilih Entitas Terlebih Dahulu --</option>');
                    return;
                }

                $.get(`/master/jabatan/get-jabatan/${entitasId}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Jabatan --</option>';
                        data.forEach(j => html +=
                            `<option value="${j.id}">${j.nama_jabatan}</option>`);
                        $('#jabatanSelect').html(html).prop('disabled', false);
                    });
            });

            // Jabatan -> Jenis
            $('#jabatanSelect').on('change', function() {
                const jabatanId = $(this).val();
                $('#jenisSelect').prop('disabled', true).html('<option value="">Memuat...</option>');
                $('#gradeSelect').prop('disabled', true).html(
                    '<option value="">-- Pilih Jenis Jabatan Terlebih Dahulu --</option>');

                if (!jabatanId) {
                    $('#jenisSelect').prop('disabled', true).html(
                        '<option value="">-- Pilih Jabatan Terlebih Dahulu --</option>');
                    return;
                }

                $.get(`/master/jabatan/get-jenis/${jabatanId}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Jenis Jabatan --</option>';
                        data.forEach(j => html +=
                            `<option value="${j.id}">${j.jenis_jabatan}</option>`);
                        $('#jenisSelect').html(html).prop('disabled', false);
                    });
            });

            // Jenis -> Grade
            $('#jenisSelect').on('change', function() {
                const jenisId = $(this).val();
                $('#gradeSelect').prop('disabled', true).html('<option value="">Memuat...</option>');

                if (!jenisId) {
                    $('#gradeSelect').prop('disabled', true).html(
                        '<option value="">-- Pilih Jenis Jabatan Terlebih Dahulu --</option>');
                    return;
                }

                $.get(`/master/jabatan/get-grade/${jenisId}`)
                    .done(function(data) {
                        let html = '<option value="">-- Pilih Grade --</option>';
                        data.forEach(g => html +=
                            `<option value="${g.id}">${g.grade}</option>`);
                        $('#gradeSelect').html(html).prop('disabled', false);
                    });
            });
        });
    </script>
@endpush
