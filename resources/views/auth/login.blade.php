<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - KIPER</title>

    <link rel="shortcut icon" href="{{ asset('template-admin/img/logo-kiper.png') }}" />

    {{-- CSS Template --}}
    <link rel="stylesheet" href="{{ asset('template-admin/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('template-admin/css/custom.css') }}">

    {{-- Bootstrap --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    {{-- Icon Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* === 1. ANIMASI === */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 40px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes pulseLogo {
            0% {
                transform: scale(1.1);
            }

            50% {
                transform: scale(1.15);
            }

            100% {
                transform: scale(1.1);
            }
        }

        /* === 2. BACKGROUND & OVERLAY === */
        body {
            background-image: url("{{ asset('template-admin/img/biktren.jpeg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            /* Mencegah scrollbar */
        }

        /* Overlay Gelap Transparan */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Gelapkan background biar teks terbaca */
            z-index: -1;
        }

        /* === 3. KOMPONEN === */
        .logo-kiper {
            animation: pulseLogo 3s infinite ease-in-out;
            display: inline-block;
        }

        /* Card Login (Glassmorphism) */
        .card-login {
            /* PERBAIKAN RESPONSIF: Gunakan max-width agar aman di HP kecil */
            width: 100%;
            max-width: 380px;
            margin: 0 15px;
            /* Jarak aman kiri kanan di HP */

            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Teks Footer */
        .footer-text {
            color: #ffffff !important;
            font-weight: 400;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.9);
            margin-top: 1.5rem;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
            animation-delay: 0.5s;
        }

        /* Input Styles */
        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }

        .input-group-text {
            border-color: #dee2e6;
        }

        .form-control:focus+.input-group-text {
            border-color: #198754;
        }
    </style>
</head>

<body class="d-flex flex-column align-items-center justify-content-center">

    {{-- KOTAK LOGIN --}}
    <div class="card shadow-lg border-0 card-login">
        <div class="card-body p-4">

            {{-- Header Logo --}}
            <div class="text-center mb-4">
                <img src="{{ asset('template-admin/img/logo-kiper.png') }}" alt="Logo Kadar" width="100"
                    class="mb-2 logo-kiper">
                <h5 class="mt-3 mb-0 fw-bold text-dark">KIPER</h5>
                <small class="text-muted">(Kinerja Pengurus)</small>
            </div>

            {{-- Notifikasi Error --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div style="font-size: 0.9rem;">
                        {{ $errors->first() }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 0.9rem;">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Form Login --}}
            <form method="POST" action="{{ route('login.process') }}">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold text-secondary small">Username</label>
                    <input type="text" autofocus name="username" id="username"
                        class="form-control @error('username') is-invalid @enderror" placeholder="Masukkan Username"
                        value="{{ old('username') }}" required>
                </div>

                <div class="mb-3 position-relative">
                    <label for="password" class="form-label fw-semibold text-secondary small">Kata Sandi</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan Kata Sandi" required>

                        <span class="input-group-text bg-white" id="togglePassword" style="cursor: pointer;">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success fw-bold py-2">Masuk</button>
                </div>
            </form>
        </div>
    </div>

    {{-- FOOTER --}}
    {{-- Perbaikan: Menghapus class 'text-muted' agar tidak bentrok dengan CSS putih --}}
    <div class="mt-4 text-center footer-text" style="font-size: 0.9rem;">
        <p class="mb-1">
            &copy; {{ date('Y') }} <strong>Nurul Jadid</strong>. All rights reserved.
        </p>
        <p>
            Developed by
            <a href="https://www.instagram.com/waseptian_11?igsh=MTlneGhycWNqZmQyeQ==" target="_blank"
                style="text-decoration: none; font-weight: bold; color: inherit;">
                Muhammad Babun Waseptian
            </a>
        </p>
    </div>

    {{-- JS Template --}}
    <script src="{{ asset('template-admin/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Script Show/Hide Password --}}
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const icon = document.querySelector('#toggleIcon');

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>

</body>

</html>
