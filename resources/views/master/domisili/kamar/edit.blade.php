@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h2 class="fw-bold mb-0">Edit Kamar</h2>
            </div>
            <a href="{{ route('master.domisili.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i data-feather="arrow-left" style="width: 16px;"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('master.domisili.kamar.update', $kamar->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Wilayah</label>
                        <select name="wilayah_id" id="wilayah_id"
                            class="form-select @error('wilayah_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach ($wilayah as $w)
                                <option value="{{ $w->id }}"
                                    {{ old('wilayah_id', $kamar->wilayah_id) == $w->id ? 'selected' : '' }}>
                                    {{ $w->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Daerah</label>
                        <select name="daerah_id" id="daerah_id" class="form-select @error('daerah_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Daerah --</option>
                            @foreach ($daerah as $d)
                                <option value="{{ $d->id }}"
                                    {{ old('daerah_id', $kamar->daerah_id) == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama_daerah }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Kamar</label>
                        <input type="text" name="nomor_kamar"
                            class="form-control @error('nomor_kamar') is-invalid @enderror"
                            value="{{ old('nomor_kamar', $kamar->nomor_kamar) }}" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            <i data-feather="save" class="me-1" style="width: 16px;"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.feather) feather.replace();

            // Logika AJAX untuk sinkronisasi Wilayah -> Daerah
            const wilayahSelect = document.getElementById('wilayah_id');
            const daerahSelect = document.getElementById('daerah_id');

            wilayahSelect.addEventListener('change', function() {
                const wilayahId = this.value;
                daerahSelect.innerHTML = '<option value="">Memuat...</option>';

                if (wilayahId) {
                    fetch("{{ route('master.domisili.ajax.daerah', '') }}/" + wilayahId)
                        .then(response => response.json())
                        .then(data => {
                            daerahSelect.innerHTML = '<option value="">-- Pilih Daerah --</option>';
                            data.forEach(daerah => {
                                const option = document.createElement('option');
                                option.value = daerah.id;
                                option.text = daerah.nama_daerah;
                                daerahSelect.appendChild(option);
                            });
                        });
                } else {
                    daerahSelect.innerHTML = '<option value="">-- Pilih Daerah --</option>';
                }
            });
        });
    </script>
@endsection
