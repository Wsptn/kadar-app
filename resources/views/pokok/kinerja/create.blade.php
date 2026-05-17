@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="card-title mb-4 fw-bold text-success">Form Penilaian Kinerja Pengurus</h5>
                <form action="{{ route('pokok.kinerja.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Pengurus <span class="text-danger">*</span></label>
                            {{-- Dropdown otomatis terkunci jika $selected_id dikirim dari controller --}}
                            <select name="pengurus_id" class="form-select" required
                                {{ isset($selected_id) ? 'style=pointer-events:none;background-color:#e9ecef;' : '' }}>
                                <option value="">-- Pilih Pengurus --</option>
                                @foreach ($pengurus as $p)
                                    <option value="{{ $p->id }}"
                                        {{ isset($selected_id) && $selected_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} {{ $p->niup ? '(' . $p->niup . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if (isset($selected_id))
                                <small class="text-success"><i data-feather="check-circle" style="width: 12px;"></i>
                                    Pengurus otomatis terpilih.</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Penilaian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_penilaian" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                    </div>

                    {{-- Tabel Skor Sesuai SOP --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">Aspek Penilaian</th>
                                    <th style="width: 45%;">Indikator & Keterangan</th>
                                    <th style="width: 10%;">Bobot</th>
                                    <th style="width: 15%;">Skor (1-100)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Kelompokkan instrumen berdasarkan aspek
                                    $groupedInstrumens = $instrumens->groupBy('aspek');
                                    $no = 1;
                                @endphp

                                @forelse ($groupedInstrumens as $aspek => $items)
                                    @foreach ($items as $index => $item)
                                        <tr>
                                            @if ($index == 0)
                                                <td rowspan="{{ count($items) }}" class="text-center fw-bold fs-5">{{ $no++ }}</td>
                                                <td rowspan="{{ count($items) }}" class="fw-bold bg-light">{{ $aspek }}</td>
                                            @endif
                                            <td>
                                                <strong>{{ $item->indikator }}</strong><br>
                                                <small class="text-muted">{{ $item->keterangan }}</small>
                                            </td>
                                            <td class="text-center fw-bold">{{ $item->bobot }}%</td>
                                            <td><input type="number" name="skor_{{ $item->id }}" class="form-control text-center"
                                                    min="0" max="100" placeholder="0" required></td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Data instrumen belum tersedia. Silakan atur di menu Master Instrumen.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-3 border rounded bg-light">
                        <label class="form-label fw-bold">Catatan / Uraian Penilai (Opsional)</label>
                        <p class="small text-muted mb-2">Uraikan tanggapan dan penilaian Saudara mengenai yang bersangkutan
                            selama melaksanakan tugas.</p>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tulis catatan di sini..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('pokok.kinerja.index') }}" class="btn btn-secondary me-2 shadow-sm">Batal</a>
                        <button type="submit" class="btn btn-success shadow-sm">
                            <i data-feather="save" style="width: 16px;"></i> Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.feather) feather.replace();
        });
    </script>
@endsection
