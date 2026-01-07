@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Data Pendidikan</h1>
            <a href="{{ route('master.pendidikan.index') }}" class="btn btn-secondary">
                <i data-feather="book"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Pendidikan</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.pendidikan.store') }}" method="POST">
                    @csrf

                    {{-- Nama Pendidikan --}}
                    <div class="mb-3">
                        <label class="form-label">Pendidikan</label>
                        <input type="text" name="nama_pendidikan"
                            class="form-control @error('nama_pendidikan') is-invalid @enderror"
                            value="{{ old('nama_pendidikan') }}" required>

                        @error('nama_pendidikan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="4" class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Tambahkan keterangan jika ada">{{ old('keterangan') }}</textarea>

                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-end pt-2">
                        <button type="submit" class="btn btn-success px-4">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
