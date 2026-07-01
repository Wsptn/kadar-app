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

    /* === CUSTOM NAVBAR HEADER (LIGHT MODE) === */
    .navbar-bg {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;
    }
    /* Mengubah ikon hamburger agar putih */
    .hamburger, .hamburger:before, .hamburger:after {
        background: #ffffff !important;
    }

    /* === DARK MODE === */
    body.dark-mode, .dark-mode .main, .dark-mode .content, .dark-mode .wrapper {
        background-color: #121212 !important;
        background-image: radial-gradient(#2d2d2d 1.5px, transparent 1.5px) !important;
        color: #e0e0e0;
    }
    .dark-mode .sidebar, .dark-mode .sidebar-content {
        background-color: #1a1a1a !important;
    }
    .dark-mode .sidebar-link, 
    .dark-mode .sidebar-link span, 
    .dark-mode .sidebar-link i, 
    .dark-mode .sidebar-link svg, 
    .dark-mode .sidebar-header, 
    .dark-mode .sidebar-brand {
        color: #cccccc !important;
    }
    .dark-mode .sidebar-link:hover, 
    .dark-mode .sidebar-link:hover span,
    .dark-mode .sidebar-link:hover svg,
    .dark-mode .sidebar-item.active .sidebar-link,
    .dark-mode .sidebar-item.active .sidebar-link span,
    .dark-mode .sidebar-item.active .sidebar-link svg {
        color: #ffffff !important;
        background-color: #2d2d2d !important;
    }
    .dark-mode .navbar, .dark-mode .navbar-bg {
        background: #1a1a1a !important;
        border-bottom: 1px solid #333 !important;
    }
    .dark-mode .card, .dark-mode .portal-card, .dark-mode .dropdown-menu {
        background-color: #1e1e1e !important;
        color: #e0e0e0 !important;
        border-color: #333 !important;
    }
    .dark-mode .text-dark { color: #e0e0e0 !important; }
    .dark-mode .text-muted { color: #aaaaaa !important; }
    .dark-mode .bg-white { background-color: #1e1e1e !important; }
    .dark-mode .table { color: #e0e0e0 !important; }
    .dark-mode .table thead th { background-color: #2c2c2c !important; color: #fff !important; }
    .dark-mode .table-hover tbody tr:hover { background-color: #2d2d2d !important; }
    .dark-mode .pill-soft, .dark-mode .prayer-box { background: #2c2c2c !important; color: #e0e0e0 !important; border: 1px solid #444; }
    .dark-mode .pill-icon { background: #3d3d3d !important; }
    .dark-mode .prayer-box.active { background: #28a745 !important; color: white !important; }
    .dark-mode h1, .dark-mode h2, .dark-mode h3, .dark-mode h4, .dark-mode h5, .dark-mode h6, .dark-mode .card-title, .dark-mode .stat-value, .dark-mode .section-title {
        color: #f8f9fa !important;
    }
    .dark-mode .time-header {
        background: linear-gradient(135deg, #0f3d1b 0%, #1e7e34 100%) !important;
    }
    .dark-mode .time-clock, .dark-mode .fw-bolder { color: #e0e0e0 !important; }
    .dark-mode .section-icon { background: #2c2c2c !important; }
    .dark-mode .form-control, .dark-mode .form-select {
        background-color: #2c2c2c;
        border-color: #444;
        color: #fff;
    }
    .dark-mode .btn-outline-secondary { border-color: #555; color: #ccc; }
    .dark-mode .btn-outline-secondary:hover { background-color: #444; color: #fff; }
</style>
