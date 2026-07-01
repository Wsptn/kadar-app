@extends('layouts.app')

@section('this-page-style')
<style>
    .btn-gradient-green {
        background: linear-gradient(135deg, #198754, #146c43) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(25, 135, 84, 0.2) !important;
    }
    .btn-gradient-green:hover {
        background: linear-gradient(135deg, #146c43, #0f5132) !important;
        box-shadow: 0 6px 8px rgba(25, 135, 84, 0.3) !important;
    }
</style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="card-title mb-4 fw-bold text-success">Form Penilaian Kinerja Pengurus</h5>
                <form action="{{ route('pokok.kinerja.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Nama Pengurus <span class="text-danger">*</span></label>
                            {{-- Dropdown otomatis terkunci jika $selected_id dikirim dari controller --}}
                            <select name="pengurus_id" id="pengurus_id" class="form-select" required
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
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Kapasitas Penilaian <span class="text-danger">*</span></label>
                            <select name="kapasitas" id="kapasitas" class="form-select" required>
                                <option value="">-- Pilih Kapasitas --</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_penilaian" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Triwulan <span class="text-danger">*</span></label>
                            <select name="triwulan" class="form-select" required>
                                <option value="1" {{ ceil(date('m') / 3) == 1 ? 'selected' : '' }}>Triwulan 1</option>
                                <option value="2" {{ ceil(date('m') / 3) == 2 ? 'selected' : '' }}>Triwulan 2</option>
                                <option value="3" {{ ceil(date('m') / 3) == 3 ? 'selected' : '' }}>Triwulan 3</option>
                                <option value="4" {{ ceil(date('m') / 3) == 4 ? 'selected' : '' }}>Triwulan 4</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
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
                                            <td>
                                                <input type="number" name="skor_{{ $item->id }}" 
                                                    class="form-control text-center @error('skor_' . $item->id) is-invalid @enderror"
                                                    placeholder="0" required 
                                                    value="{{ old('skor_' . $item->id) }}"
                                                    oninvalid="this.setCustomValidity('Skor wajib diisi.')" 
                                                    oninput="this.setCustomValidity(''); checkSkor(this);">
                                                @error('skor_' . $item->id)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
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

            @php
                $pengurusMapped = $pengurus->mapWithKeys(function($p) {
                    return [$p->id => [
                        'jabatans' => $p->riwayatJabatans->map(function($j) {
                            return ['id' => 'jabatan_' . $j->id, 'nama' => '[Jabatan] ' . ($j->strukturJabatan->jabatan ?? '') . ' - ' . ($j->strukturJabatan->entitas ?? '')];
                        }),
                        'tugas' => $p->riwayatTugas->map(function($t) {
                            $jenis = ucfirst($t->masterTugas->jenis_tugas ?? 'Tugas');
                            return ['id' => 'tugas_' . $t->id, 'nama' => '[' . $jenis . '] ' . ($t->masterTugas->nama_tugas ?? '')];
                        })
                    ]];
                });
            @endphp
            
            const pengurusData = @json($pengurusMapped);

            const selectPengurus = document.getElementById('pengurus_id');
            const selectKapasitas = document.getElementById('kapasitas');

            window.checkSkor = function(input) {
                if (input.value !== '') {
                    let val = parseInt(input.value);
                    if (val > 100) {
                        input.value = 100;
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: 'Skor tidak boleh lebih dari 100!',
                                confirmButtonText: 'Mengerti',
                                customClass: {
                                    confirmButton: 'btn-gradient-green'
                                }
                            });
                        } else {
                            alert('Skor tidak boleh lebih dari 100!');
                        }
                    } else if (val < 0) {
                        input.value = 0;
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: 'Skor tidak boleh kurang dari 0!',
                                confirmButtonText: 'Mengerti',
                                customClass: {
                                    confirmButton: 'btn-gradient-green'
                                }
                            });
                        } else {
                            alert('Skor tidak boleh kurang dari 0!');
                        }
                    }
                }
            };

            function updateKapasitas() {
                const id = selectPengurus.value;
                selectKapasitas.innerHTML = '<option value="">-- Pilih Kapasitas --</option>';

                if (id && pengurusData[id]) {
                    const data = pengurusData[id];
                    let hasOptions = false;
                    
                    data.jabatans.forEach(j => {
                        selectKapasitas.innerHTML += `<option value="${j.id}">${j.nama}</option>`;
                        hasOptions = true;
                    });
                    data.tugas.forEach(t => {
                        selectKapasitas.innerHTML += `<option value="${t.id}">${t.nama}</option>`;
                        hasOptions = true;
                    });

                    if (!hasOptions) {
                        selectKapasitas.innerHTML = '<option value="">-- Tidak Ada Peran Aktif --</option>';
                    }
                }
            }

            selectPengurus.addEventListener('change', updateKapasitas);

            // Trigger on load if pre-selected
            if (selectPengurus.value) {
                updateKapasitas();
            }
        });
    </script>
@endsection
