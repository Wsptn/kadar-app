@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Tambah Domisili</h1>

        <div class="bg-light p-2 mb-4 border rounded small">
            <span>Data Master / <a href="{{ route('master.domisili.index') }}"
                    class="text-decoration-none text-muted">Domisili</a> /
                <span class="text-success fw-semibold">Tambah</span></span>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('master.domisili.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="wilayah" class="form-label">Wilayah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('wilayah') is-invalid @enderror" id="wilayah"
                                name="wilayah" value="{{ old('wilayah') }}" required>
                            @error('wilayah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="daerah" class="form-label">Daerah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('daerah') is-invalid @enderror" id="daerah"
                                name="daerah" value="{{ old('daerah') }}" required>
                            @error('daerah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="entitas_daerah" class="form-label">Entitas Daerah <span class="text-muted">(Opsional)</span></label>
                            <input type="text" class="form-control @error('entitas_daerah') is-invalid @enderror"
                                id="entitas_daerah" name="entitas_daerah" value="{{ old('entitas_daerah') }}" placeholder="Contoh: KIP / dll">
                            @error('entitas_daerah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="kamar" class="form-label">Kamar <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kamar') is-invalid @enderror" id="kamar"
                                name="kamar" value="{{ old('kamar') }}" required>
                            @error('kamar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('master.domisili.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
