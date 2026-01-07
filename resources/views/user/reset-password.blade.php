@extends('layouts.app')

@section('this-page-contain')
    <div class="container-fluid px-4 py-4">
        {{-- Header Page --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 text-gray-800 fw-bold mb-1">Reset Password</h1>
            </div>
            <a href="{{ route('user.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm px-3">
                <i data-feather="arrow-left" class="me-1" style="width: 14px;"></i> Kembali
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">

                        {{-- Info User Target --}}
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded border">
                            <div class="bg-white p-2 rounded-circle shadow-sm me-3 border d-flex justify-content-center align-items-center"
                                style="width: 50px; height: 50px;">
                                <i data-feather="user" class="text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 0.7rem; letter-spacing: 0.5px;">Target User</small>
                                <h6 class="fw-bold mb-0 text-dark">{{ $user->name }}</h6>
                                <small class="text-secondary">{{ '@' . $user->username }}</small>
                            </div>
                        </div>

                        <form action="{{ route('user.reset-password.process', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Input Password Baru --}}
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold text-secondary small">Password
                                    Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i data-feather="lock" style="width: 16px;"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Masukkan password baru" required>
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted" style="font-size: 0.75rem;">
                                    Minimal 6 karakter. Kombinasi huruf & angka disarankan.
                                </div>
                            </div>

                            {{-- Input Konfirmasi Password --}}
                            <div class="mb-4">
                                <label for="password_confirmation"
                                    class="form-label fw-semibold text-secondary small">Ulangi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i data-feather="check-circle" style="width: 16px;"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0"
                                        id="password_confirmation" name="password_confirmation"
                                        placeholder="Ketik ulang password baru" required>
                                </div>
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success py-2 fw-bold shadow-sm">
                                    <i data-feather="save" class="me-2" style="width: 16px;"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('this-page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof feather !== 'undefined') feather.replace();
        });
    </script>
@endsection
