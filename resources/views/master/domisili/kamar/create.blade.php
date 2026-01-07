@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Kamar</h1>
            <a href="{{ route('master.domisili.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Kamar</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.domisili.kamar.store') }}" method="POST">
                    @csrf

                    {{-- Pilih Wilayah --}}
                    <div class="mb-3">
                        <label class="form-label">Wilayah</label>
                        <select name="wilayah_id" id="wilayahSelect"
                            class="form-select @error('wilayah_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Wilayah --</option>

                            @foreach ($wilayah as $w)
                                <option value="{{ $w->id }}" {{ old('wilayah_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>

                        @error('wilayah_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Pilih Daerah --}}
                    <div class="mb-3">
                        <label class="form-label">Daerah</label>
                        <select name="daerah_id" id="daerahSelect"
                            class="form-select @error('daerah_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Wilayah Terlebih Dahulu --</option>
                        </select>

                        @error('daerah_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nomor Kamar --}}
                    <div class="mb-3">
                        <label class="form-label">Kamar</label>
                        <input type="text" name="nomor_kamar"
                            class="form-control @error('nomor_kamar') is-invalid @enderror" value="{{ old('nomor_kamar') }}"
                            required>

                        @error('nomor_kamar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end pt-2">
                        <button type="submit" class="btn btn-success px-4">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- AJAX LOAD DAERAH BY WILAYAH --}}
    <script>
        document.getElementById('wilayahSelect').addEventListener('change', function() {
            let wilayahID = this.value;
            let daerahSelect = document.getElementById('daerahSelect');

            daerahSelect.innerHTML = '<option value="">Loading...</option>';

            if (!wilayahID) {
                daerahSelect.innerHTML = '<option value="">-- Pilih Wilayah Terlebih Dahulu --</option>';
                return;
            }

            fetch('/master/domisili/get-daerah/' + wilayahID)
                .then(response => response.json())
                .then(data => {
                    daerahSelect.innerHTML = '<option value="">-- Pilih Daerah --</option>';

                    data.forEach(function(daerah) {
                        daerahSelect.innerHTML += `
                            <option value="${daerah.id}">${daerah.nama_daerah}</option>
                        `;
                    });

                    if (data.length === 0) {
                        daerahSelect.innerHTML =
                            '<option value="">Tidak ada daerah pada wilayah ini</option>';
                    }
                })
                .catch(err => {
                    daerahSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                });
        });
    </script>
@endsection
