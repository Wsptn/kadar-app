@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Jenis Jabatan</h1>
            <a href="{{ route('master.jabatan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Jenis Jabatan</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('master.jabatan.jenis.store') }}" method="POST">
                    @csrf

                    {{-- Pilih Entitas --}}
                    <div class="mb-3">
                        <label class="form-label">Entitas</label>
                        <select id="entitasSelect" name="entitas_id"
                            class="form-select @error('entitas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Entitas --</option>

                            @foreach ($entitas as $e)
                                <option value="{{ $e->id }}">{{ $e->nama_entitas }}</option>
                            @endforeach
                        </select>
                        @error('entitas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Pilih Jabatan --}}
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <select id="jabatanSelect" name="jabatan_id"
                            class="form-select @error('jabatan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Entitas Terlebih Dahulu --</option>
                        </select>
                        @error('jabatan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nama Jenis --}}
                    <div class="mb-3">
                        <label class="form-label">Jenis Jabatan</label>
                        <input type="text" name="jenis_jabatan"
                            class="form-control @error('jenis_jabatan') is-invalid @enderror"
                            placeholder="Masukkan jenis jabatan" value="{{ old('jenis_jabatan') }}" required>
                        @error('jenis_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success px-4">Simpan</button>
                    </div>

                </form>

            </div>

        </div>

    </div>
    @push('page-scripts')
        <script>
            $(document).ready(function() {

                $('#entitasSelect').on('change', function() {
                    let entitasId = $(this).val();
                    let jabatanSelect = $('#jabatanSelect');

                    // kosongkan dulu
                    jabatanSelect.html('<option value="">Memuat data...</option>');

                    if (entitasId) {
                        $.ajax({
                            url: "{{ route('master.jabatan.jabatan.byEntitas', '') }}/" + entitasId,
                            type: "GET",
                            success: function(res) {
                                jabatanSelect.empty().append(
                                    '<option value="">-- Pilih Jabatan --</option>');

                                $.each(res, function(index, item) {
                                    jabatanSelect.append('<option value="' + item.id +
                                        '">' + item.nama_jabatan + '</option>');
                                });
                            }
                        });
                    } else {
                        jabatanSelect.html('<option value="">-- Pilih Entitas Terlebih Dahulu --</option>');
                    }
                });

            });
        </script>
    @endpush
@endsection
