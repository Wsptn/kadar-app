@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <h1 class="mb-4">Edit Instrumen Kinerja</h1>

        <div class="bg-light p-2 mb-4 border rounded small">
            <span>Data Master / Instrumen Penilaian / <span class="text-success fw-semibold">Edit</span></span>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('master.instrumen.update', $instrumen->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Aspek Utama <span class="text-danger">*</span></label>
                        <input type="text" name="aspek" class="form-control" value="{{ old('aspek', $instrumen->aspek) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Indikator Penilaian <span class="text-danger">*</span></label>
                        <input type="text" name="indikator" class="form-control" value="{{ old('indikator', $instrumen->indikator) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan / Penjelasan Singkat</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $instrumen->keterangan) }}</textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Bobot (%) <span class="text-danger">*</span></label>
                            <input type="number" name="bobot" class="form-control" value="{{ old('bobot', $instrumen->bobot) }}" required min="1" max="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status', $instrumen->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $instrumen->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('master.instrumen.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-success">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
