@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Wilayah</h1>
            <a href="{{ route('master.domisili.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Wilayah</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.domisili.wilayah.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama Wilayah</label>
                        <input type="text" name="nama_wilayah"
                            class="form-control @error('nama_wilayah') is-invalid @enderror"
                            value="{{ old('nama_wilayah') }}" required>

                        @error('nama_wilayah')
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
