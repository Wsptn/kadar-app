@extends('layouts.app')

@section('this-page-style')
    <style>
        .profile-container {
            max-width: 850px;
            margin: 0 auto;
        }

        .profile-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 25px;
            border: 1px solid #f0f0f0;
        }

        .profile-header {
            text-align: center;
        }

        .profile-img-wrapper {
            position: relative;
            display: inline-block;
        }

        .profile-header img {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-upload-foto {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid #fff;
            transition: all 0.2s;
        }

        .btn-upload-foto:hover {
            background: #218838;
            transform: scale(1.1);
        }

        .form-label {
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
        }

        .form-control {
            padding: 10px 15px;
            border-radius: 8px;
        }
    </style>
@endsection

@section('this-page-contain')
    <div class="container-fluid px-4 profile-container">

        {{-- Notifikasi Sukses/Error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i data-feather="check-circle" class="me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- BAGIAN 1: UPDATE FOTO & PROFIL --}}
        <div class="profile-card">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="profile-header">
                    <div class="profile-img-wrapper mb-3">
                        {{-- Logic Foto: Check DB ? Pakai Storage : Pakai Default --}}
                        <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : asset('template-admin/img/avatars/profile.jpg') }}"
                            alt="Foto Profil" id="previewFoto">

                        {{-- Tombol Kamera Kecil --}}
                        <label for="inputFoto" class="btn-upload-foto" title="Ganti Foto">
                            <i data-feather="camera" style="width: 16px;"></i>
                        </label>
                        <input type="file" name="foto" id="inputFoto" class="d-none" accept="image/*"
                            onchange="previewImage(this)">
                    </div>

                    <h4 class="fw-bold text-dark">{{ Auth::user()->name }}</h4>
                    <p class="text-muted mb-2">{{ Auth::user()->level }}</p>
                    <span class="badge bg-light text-dark border px-3 py-2">
                        Username: {{ Auth::user()->username }}
                    </span>
                </div>

                {{-- Input Ganti Nama (Opsional, jika ingin bisa ganti nama) --}}
                <div class="mt-4 row justify-content-center">
                    <div class="col-md-8">
                        <label class="form-label">Nama Lengkap</label>
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}"
                                required>
                            <button type="submit" class="btn btn-success">
                                <i data-feather="save" class="me-1"></i> Simpan Profil
                            </button>
                        </div>
                        <small class="text-muted">*Klik ikon kamera diatas untuk mengganti foto.</small>
                    </div>
                </div>
            </form>
        </div>

        {{-- BAGIAN 2: GANTI PASSWORD --}}
        <div class="profile-card">
            <h5 class="fw-bold mb-4 pb-2 border-bottom"><i data-feather="lock" class="me-2"></i>Ganti Password</h5>

            <form action="{{ route('profile.updatePassword') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="mb-3 col-md-12">
                        <label for="current_password" class="form-label">Password Saat Ini <span
                                class="text-danger">*</span></label>
                        <input type="password" name="current_password" id="current_password" class="form-control"
                            placeholder="Ketik password lama Anda" required>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="new_password" class="form-label">Password Baru <span
                                class="text-danger">*</span></label>
                        <input type="password" name="new_password" id="new_password" class="form-control"
                            placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="new_password_confirmation" class="form-label">Ulangi Password Baru <span
                                class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                            class="form-control" placeholder="Pastikan sama dengan password baru" required>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-dark px-4">
                        <i data-feather="refresh-cw" class="me-1"></i> Perbarui Password
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection

@section('this-page-scripts')
    {{-- Script Preview Foto --}}
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewFoto').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            if (typeof feather !== "undefined") feather.replace();
        });
    </script>
@endsection
