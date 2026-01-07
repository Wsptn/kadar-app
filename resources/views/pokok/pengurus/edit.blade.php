@extends('layouts.app')

@section('this-page-contain')
    @php
        function tampilkanBerkas($file)
        {
            if (!$file) {
                return '<small class="text-muted fst-italic">Tidak ada berkas</small>';
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $url = asset('storage/' . $file);

            // Jika gambar
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                return '<div class="mt-2"><img src="' .
                    $url .
                    '" style="width:100px; height:120px; border-radius:6px; object-fit:cover; border:1px solid #ddd;"></div>';
            }

            // Jika PDF atau file lain
            return '<div class="mt-1"><a href="' .
                $url .
                '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-text"></i> Lihat Berkas Saat Ini</a></div>';
        }
    @endphp

    <div class="container-fluid px-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Edit Pengurus</h2>
            </div>
            <a href="{{ route('pokok.pengurus.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body px-4 py-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pokok.pengurus.update', $pengurus->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- NIUP & Nama --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">NIUP <span class="text-danger">*</span></label>
                            <input type="text" name="niup" class="form-control"
                                value="{{ old('niup', $pengurus->niup) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control"
                                value="{{ old('nama', $pengurus->nama) }}" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 fw-bold text-success">Lokasi Tugas</h6>

                    {{-- WILAYAH --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wilayah</label>
                        {{-- Jika Admin/Biktren: Boleh ganti Wilayah --}}
                        @if (Auth::user()->isAdmin() || Auth::user()->isBiktren())
                            <select name="wilayah_id" id="wilayahSelect" class="form-select">
                                <option value="">-- Pilih Wilayah --</option>
                                @foreach ($wilayahs as $w)
                                    <option value="{{ $w->id }}"
                                        {{ old('wilayah_id', $pengurus->wilayah_id) == $w->id ? 'selected' : '' }}>
                                        {{ $w->nama_wilayah }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            {{-- Jika Wilayah/Daerah: Readonly --}}
                            <input type="hidden" id="wilayahSelect" name="wilayah_id" value="{{ $pengurus->wilayah_id }}">
                            <input type="text" class="form-control bg-light"
                                value="{{ $pengurus->wilayah->nama_wilayah ?? '-' }}" readonly>
                        @endif
                    </div>

                    {{-- DAERAH --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Daerah</label>
                        {{-- Admin/Biktren/Wilayah boleh ganti daerah (dalam lingkup wilayahnya) --}}
                        @if (!Auth::user()->isDaerah())
                            <select name="daerah_id" id="daerahSelect" class="form-select">
                                <option value="">-- Pilih Daerah --</option>
                                {{-- Opsi diload via JS/PHP --}}
                                @foreach ($daerahs as $d)
                                    <option value="{{ $d->id }}"
                                        {{ old('daerah_id', $pengurus->daerah_id) == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama_daerah }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            {{-- Akun Daerah: Readonly --}}
                            <input type="hidden" name="daerah_id" value="{{ $pengurus->daerah_id }}">
                            <input type="text" class="form-control bg-light"
                                value="{{ $pengurus->daerah->nama_daerah ?? '-' }}" readonly>
                        @endif
                    </div>

                    {{-- === ENTITAS DAERAH (MANUAL LIST) === --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Entitas Daerah</label>

                        <select name="entitas_daerah" class="form-select @error('entitas_daerah') is-invalid @enderror">
                            <option value="">-- Pilih Entitas Daerah (Opsional) --</option>
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
                                ]; // Tambahkan opsi lain di sini
                            @endphp
                            @foreach ($opsiEntitas as $op)
                                <option value="{{ $op }}"
                                    {{ old('entitas_daerah', $pengurus->entitas_daerah) == $op ? 'selected' : '' }}>
                                    {{ $op }}
                                </option>
                            @endforeach
                        </select>

                        @error('entitas_daerah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- =================================================== --}}

                    {{-- KAMAR --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kamar</label>
                        <select name="kamar_id" id="kamarSelect" class="form-select">
                            <option value="">-- Pilih Kamar --</option>
                            @foreach ($kamars as $k)
                                <option value="{{ $k->id }}"
                                    {{ old('kamar_id', $pengurus->kamar_id) == $k->id ? 'selected' : '' }}>
                                    {{ $k->nomor_kamar ?? ($k->nama_kamar ?? $k->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 fw-bold text-success">Kelembagaan</h6>

                    {{-- ENTITAS --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Entitas</label>
                        <select name="entitas_id" id="entitasSelect" class="form-select">
                            <option value="">-- Pilih Entitas --</option>
                            @foreach ($entitas as $e)
                                <option value="{{ $e->id }}"
                                    {{ old('entitas_id', $pengurus->entitas_id) == $e->id ? 'selected' : '' }}>
                                    {{ $e->nama_entitas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- JABATAN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jabatan</label>
                        <select name="jabatan_id" id="jabatanSelect" class="form-select">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach ($jabatans as $j)
                                <option value="{{ $j->id }}"
                                    {{ old('jabatan_id', $pengurus->jabatan_id) == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- JENIS JABATAN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Jabatan</label>
                        <select name="jenis_jabatan_id" id="jenisSelect" class="form-select">
                            <option value="">-- Pilih Jenis Jabatan --</option>
                            @foreach ($jenis_jabatans as $jj)
                                <option value="{{ $jj->id }}"
                                    {{ old('jenis_jabatan_id', $pengurus->jenis_jabatan_id) == $jj->id ? 'selected' : '' }}>
                                    {{ $jj->jenis_jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- GRADE --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Grade Jabatan</label>
                        <select name="grade_jabatan_id" id="gradeSelect" class="form-select">
                            <option value="">-- Pilih Grade --</option>
                            @foreach ($grade_jabatans as $g)
                                <option value="{{ $g->id }}"
                                    {{ old('grade_jabatan_id', $pengurus->grade_jabatan_id) == $g->id ? 'selected' : '' }}>
                                    {{ $g->grade }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">SK Kepengurusan</label>
                        <input type="text" name="sk_kepengurusan" class="form-control"
                            value="{{ old('sk_kepengurusan', $pengurus->sk_kepengurusan) }}">
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 fw-bold text-success">Data Pendukung</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fungsional Tugas</label>
                        <select name="fungsional_tugas_id" class="form-select">
                            <option value="">-- Pilih --</option>
                            @foreach ($fungsionalTugas as $ft)
                                <option value="{{ $ft->id_tugas }}"
                                    {{ old('fungsional_tugas_id', $pengurus->fungsional_tugas_id) == $ft->id_tugas ? 'selected' : '' }}>
                                    {{ $ft->tugas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Rangkap Internal</label>
                            <select name="rangkap_internal_id" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach ($rangkapInternals as $ri)
                                    <option value="{{ $ri->id_internal }}"
                                        {{ old('rangkap_internal_id', $pengurus->rangkap_internal_id) == $ri->id_internal ? 'selected' : '' }}>
                                        {{ $ri->internal }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Rangkap Eksternal</label>
                            <select name="rangkap_eksternal_id" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach ($rangkapEksternals as $re)
                                    <option value="{{ $re->id_eksternal }}"
                                        {{ old('rangkap_eksternal_id', $pengurus->rangkap_eksternal_id) == $re->id_eksternal ? 'selected' : '' }}>
                                        {{ $re->eksternal }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Pendidikan</label>
                            <select name="pendidikan_id" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach ($pendidikans as $pd)
                                    <option value="{{ $pd->id_pendidikan }}"
                                        {{ old('pendidikan_id', $pengurus->pendidikan_id) == $pd->id_pendidikan ? 'selected' : '' }}>
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
                                        {{ old('angkatan_id', $pengurus->angkatan_id) == $ag->id_angkatan ? 'selected' : '' }}>
                                        {{ $ag->angkatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif" {{ old('status', $pengurus->status) == 'aktif' ? 'selected' : '' }}>
                                Aktif</option>
                            <option value="non_aktif"
                                {{ old('status', $pengurus->status) == 'non_aktif' ? 'selected' : '' }}>Non Aktif
                            </option>
                        </select>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 fw-bold text-success">Berkas & Foto</h6>

                    {{-- FOTO (Semua boleh update) --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Foto Profil</label>
                        <input type="file" name="foto" class="form-control">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                        <div class="mt-2">
                            {!! tampilkanBerkas($pengurus->foto) !!}
                        </div>
                    </div>

                    {{-- BERKAS LAIN (Hanya Admin & Biktren) --}}
                    @if (Auth::user()->isAdmin() || Auth::user()->isBiktren())
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Berkas SK Pengurus</label>
                                <input type="file" name="berkas_sk_pengurus" class="form-control">
                                {!! tampilkanBerkas($pengurus->berkas_sk_pengurus) !!}
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Berkas Surat Tugas</label>
                                <input type="file" name="berkas_surat_tugas" class="form-control">
                                {!! tampilkanBerkas($pengurus->berkas_surat_tugas) !!}
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Berkas PLT</label>
                                <input type="file" name="berkas_plt" class="form-control">
                                {!! tampilkanBerkas($pengurus->berkas_plt) !!}
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Berkas Lain</label>
                                <input type="file" name="berkas_lain" class="form-control">
                                {!! tampilkanBerkas($pengurus->berkas_lain) !!}
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                Anda hanya diizinkan mengubah <strong>Foto Profil</strong>. Untuk mengubah berkas dokumen
                                lainnya, silahkan hubungi Admin Pusat atau BIKTREN.
                            </div>
                        </div>
                    @endif

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection

@section('this-page-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Helper function untuk fetch JSON
        async function fetchJson(url) {
            try {
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) return null;
                return await res.json();
            } catch (err) {
                return null;
            }
        }

        // Variable data lama dari backend (untuk prefill saat gagal validasi)
        const oldWilayah = "{{ old('wilayah_id', $pengurus->wilayah_id) }}";
        const oldDaerah = "{{ old('daerah_id', $pengurus->daerah_id) }}";
        const oldKamar = "{{ old('kamar_id', $pengurus->kamar_id) }}";

        const oldEntitas = "{{ old('entitas_id', $pengurus->entitas_id) }}";
        const oldJabatan = "{{ old('jabatan_id', $pengurus->jabatan_id) }}";
        const oldJenis = "{{ old('jenis_jabatan_id', $pengurus->jenis_jabatan_id) }}";
        const oldGrade = "{{ old('grade_jabatan_id', $pengurus->grade_jabatan_id) }}";

        // ========== DOMISILI LOGIC ==========

        // Load Daerah by Wilayah
        async function loadDaerah(wid, selectedId = null) {
            const el = document.getElementById('daerahSelect');
            if (!el) return; // jika element tidak ada (misal akun daerah)

            el.innerHTML = '<option>Memuat...</option>';
            el.disabled = true;

            const data = await fetchJson(`/master/domisili/get-daerah/${wid}`);
            if (data) {
                let html = '<option value="">-- Pilih Daerah --</option>';
                data.forEach(d => {
                    let sel = (selectedId == d.id) ? 'selected' : '';
                    html += `<option value="${d.id}" ${sel}>${d.nama_daerah}</option>`;
                });
                el.innerHTML = html;
                el.disabled = false;

                // Jika ada selectedId, load anak-anaknya (kamar)
                if (selectedId) loadKamar(selectedId, oldKamar);
            } else {
                el.innerHTML = '<option>Gagal memuat</option>';
            }
        }

        // Load Kamar by Daerah
        async function loadKamar(did, selectedId = null) {
            const el = document.getElementById('kamarSelect');
            el.innerHTML = '<option>Memuat...</option>';
            el.disabled = true;

            const data = await fetchJson(`/master/domisili/get-kamar/${did}`);
            if (data) {
                let html = '<option value="">-- Pilih Kamar --</option>';
                data.forEach(k => {
                    let sel = (selectedId == k.id) ? 'selected' : '';
                    let name = k.nomor_kamar || k.nama_kamar || k.id;
                    html += `<option value="${k.id}" ${sel}>${name}</option>`;
                });
                el.innerHTML = html;
                el.disabled = false;
            } else {
                el.innerHTML = '<option>Gagal memuat</option>';
            }
        }

        // Event Listeners Domisili
        document.getElementById('wilayahSelect')?.addEventListener('change', function() {
            loadDaerah(this.value);
            document.getElementById('kamarSelect').innerHTML = '<option>-- Pilih Daerah Dulu --</option>';
        });

        document.getElementById('daerahSelect')?.addEventListener('change', function() {
            loadKamar(this.value);
        });


        // ========== KELEMBAGAAN LOGIC ==========

        async function loadJabatan(eid, selectedId = null) {
            const el = document.getElementById('jabatanSelect');
            el.innerHTML = '<option>Memuat...</option>';
            el.disabled = true;

            const data = await fetchJson(`/master/jabatan/get-jabatan/${eid}`);
            if (data) {
                let html = '<option value="">-- Pilih Jabatan --</option>';
                data.forEach(j => {
                    let sel = (selectedId == j.id) ? 'selected' : '';
                    html += `<option value="${j.id}" ${sel}>${j.nama_jabatan}</option>`;
                });
                el.innerHTML = html;
                el.disabled = false;
            }
        }

        async function loadJenis(jid, selectedId = null) {
            const el = document.getElementById('jenisSelect');
            el.innerHTML = '<option>Memuat...</option>';
            el.disabled = true;

            const data = await fetchJson(`/master/jabatan/get-jenis/${jid}`);
            if (data) {
                let html = '<option value="">-- Pilih Jenis --</option>';
                data.forEach(jj => {
                    let sel = (selectedId == jj.id) ? 'selected' : '';
                    html += `<option value="${jj.id}" ${sel}>${jj.jenis_jabatan}</option>`;
                });
                el.innerHTML = html;
                el.disabled = false;
            }
        }

        async function loadGrade(jid, selectedId = null) {
            const el = document.getElementById('gradeSelect');
            el.innerHTML = '<option>Memuat...</option>';
            el.disabled = true;

            const data = await fetchJson(`/master/jabatan/get-grade/${jid}`);
            if (data) {
                let html = '<option value="">-- Pilih Grade --</option>';
                data.forEach(g => {
                    let sel = (selectedId == g.id) ? 'selected' : '';
                    html += `<option value="${g.id}" ${sel}>${g.grade}</option>`;
                });
                el.innerHTML = html;
                el.disabled = false;
            }
        }

        // Event Listeners Kelembagaan
        document.getElementById('entitasSelect').addEventListener('change', function() {
            loadJabatan(this.value);
            document.getElementById('jenisSelect').innerHTML = '<option>-- Pilih Jabatan Dulu --</option>';
            document.getElementById('gradeSelect').innerHTML = '<option>-- Pilih Jenis Dulu --</option>';
        });

        document.getElementById('jabatanSelect').addEventListener('change', function() {
            loadJenis(this.value);
            document.getElementById('gradeSelect').innerHTML = '<option>-- Pilih Jenis Dulu --</option>';
        });

        document.getElementById('jenisSelect').addEventListener('change', function() {
            loadGrade(this.value);
        });


        // ========== INIT ON LOAD (Hanya jika dropdown kosong/perlu di-refresh) ==========
        $(function() {
            const wilayahEl = document.getElementById('wilayahSelect');
            if (wilayahEl && wilayahEl.value && document.getElementById('daerahSelect').options.length <= 1) {
                loadDaerah(wilayahEl.value, oldDaerah);
            }
        });
    </script>
@endsection
