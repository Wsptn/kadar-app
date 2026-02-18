{{-- Sidebar --}}
<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="/dashboard">
            <img src="{{ asset('template-admin/img/logo-kiper.png') }}" alt="Logo"
                style="width: 50px; height:auto; margin-right: 10px;">
            <span class="align-middle">KIPER</span>
        </a>

        <ul class="sidebar-nav">

            <li class="sidebar-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('dashboard.index') }}">
                    <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-header">DATA POKOK</li>

            <li class="sidebar-item {{ request()->routeIs('pokok.pengurus.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('pokok.pengurus.index') }}">
                    <i class="align-middle" data-feather="users"></i> <span class="align-middle">Pengurus</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('pokok.waliasuh.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('pokok.waliasuh.index') }}">
                    <i class="align-middle" data-feather="user-check"></i>
                    <span class="align-middle">Wali Asuh</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('pokok.pengajar.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('pokok.pengajar.index') }}">
                    <i class="align-middle" data-feather="book-open"></i>
                    <span class="align-middle">Pengajar</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('pokok.muallim.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('pokok.muallim.index') }}">
                    <i class="align-middle" data-feather="book"></i>
                    <span class="align-middle">Mu’allim</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('pokok.kinerja.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('pokok.kinerja.index') }}">
                    <i class="align-middle" data-feather="bar-chart-2"></i>
                    <span class="align-middle">Kinerja & Rekomendasi</span>
                </a>
            </li>

            <li class="sidebar-header">DATA MASTER</li>

            <li class="sidebar-item {{ request()->routeIs('master.domisili.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('master.domisili.index') }}">
                    <i class="align-middle" data-feather="home"></i>
                    <span class="align-middle">Master Domisili</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('master.jabatan.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('master.jabatan.index') }}">
                    <i class="align-middle" data-feather="briefcase"></i>
                    <span class="align-middle">Master Jabatan</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('master.tugas.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('master.tugas.index') }}">
                    <i class="align-middle" data-feather="clipboard"></i>
                    <span class="align-middle">Master Fungsional Tugas</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('master.internal.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('master.internal.index') }}">
                    <i class="align-middle" data-feather="git-merge"></i>
                    <span class="align-middle">Master Rangkap Internal</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('master.eksternal.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('master.eksternal.index') }}">
                    <i class="align-middle" data-feather="git-branch"></i>
                    <span class="align-middle">Master Rangkap Eksternal</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('master.pendidikan.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('master.pendidikan.index') }}">
                    <i class="align-middle" data-feather="bookmark"></i>
                    <span class="align-middle">Master Pendidikan</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('master.angkatan.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('master.angkatan.index') }}">
                    <i class="align-middle" data-feather="layers"></i>
                    <span class="align-middle">Master Angkatan</span>
                </a>
            </li>

            {{-- <li class="sidebar-item {{ request()->routeIs('master.berkas.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('master.berkas.index') }}">
                    <i class="align-middle" data-feather="folder"></i>
                    <span class="align-middle">Master Jenis Berkas</span>
                </a>
            </li> --}}

            @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isBiktren()))
                <li class="sidebar-header">USERS ACCOUNT</li>
            @endif

            @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isBiktren()))
                <li class="sidebar-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('user.index') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">User Account</span>
                    </a>
                </li>
            @endif


            <li class="sidebar-item {{ request()->routeIs('profile.index.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('profile.index') }}">
                    <i class="align-middle" data-feather="settings"></i>
                    <span class="align-middle">Profile</span>
                </a>
            </li>
        </ul>

    </div>
</nav>
