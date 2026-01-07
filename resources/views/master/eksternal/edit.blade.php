@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Edit Data</h1>
            <a href="{{ route('master.eksternal.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Tugas Eksternal</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.eksternal.update', $eksternal->id_eksternal) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tugas Eksternal</label>
                        <input type="text" name="eksternal" class="form-control @error('eksternal') is-invalid @enderror"
                            value="{{ old('eksternal', $eksternal->eksternal) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="4" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $eksternal->keterangan) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end pt-2">
                        <button type="submit" class="btn btn-success px-4">Simpan</button>
                    </div>
                </form>


            </div>
        </div>
    </div>
@endsection
