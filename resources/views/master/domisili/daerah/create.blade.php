@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Daerah</h1>
            <a href="{{ route('master.domisili.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Daerah</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.domisili.daerah.store') }}" method="POST">
                    @csrf

                    {{-- Pilih Wilayah --}}
                    <div class="mb-3">
                        <label class="form-label">Wilayah</label>
                        <select name="wilayah_id" class="form-select @error('wilayah_id') is-invalid @enderror" required>
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

                    {{-- Nama Daerah --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Daerah</label>
                        <input type="text" name="nama_daerah"
                            class="form-control @error('nama_daerah') is-invalid @enderror" value="{{ old('nama_daerah') }}"
                            required>

                        @error('nama_daerah')
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
@endsection
