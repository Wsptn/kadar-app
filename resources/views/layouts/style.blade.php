{{-- <link href="css/app.css" rel="stylesheet"> --}}
<link href="{{ asset('template-admin/css/app.css') }}" rel="stylesheet">
<link href="{{ asset('template-admin/css/custom.css') }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
<style>
    .btn-success {
        background-color: #28a745;
        border: none;
        border-radius: 5px;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-secondary {
        border-radius: 5px;
    }

    table th,
    table td {
        vertical-align: middle !important;
    }

    .table thead th {
        background-color: #eaf7ea !important;
        color: #000000 !important;
        font-weight: 600;
    }

    /* Input Search – Hover hijau */
    .custom-search-input:hover {
        border-color: #28a745 !important;
        /* Warna hijau */
    }

    /* Input Search – Focus (klik) hijau */
    .custom-search-input:focus {
        border-color: #28a745 !important;
        box-shadow: 0 0 6px rgba(40, 167, 69, 0.35) !important;
        /* Glow hijau */
    }

    /* Biar smooth transition */
    .custom-search-input {
        transition: all 0.25s ease-in-out;
    }

    .form-control,
    .form-select {
        border-radius: 5px;
        border: 1px solid #ced4da;
        transition: all 0.2s ease-in-out;

    }

    .form-control:focus,
    .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    /* === TAMBAHAN CSS UNTUK PAGINATION HIJAU === */
    .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 5px;
        /* Jarak antar kotak */
    }

    .page-link {
        position: relative;
        display: block;
        color: #28a745;
        /* Teks warna Hijau */
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        /* Sudut melengkung */
        padding: 0.375rem 0.75rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .page-link:hover {
        z-index: 2;
        color: #1e7e34;
        /* Hijau lebih gelap saat hover */
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #28a745;
        /* Background Hijau saat aktif */
        border-color: #28a745;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
</style>
