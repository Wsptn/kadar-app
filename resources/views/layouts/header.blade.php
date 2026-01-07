<nav class="navbar navbar-expand navbar-light navbar-bg">

    {{-- Tombol Toggle Sidebar --}}
    <a class="sidebar-toggle js-sidebar-toggle">
        <i class="hamburger align-self-center"></i>
    </a>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">

            {{-- Cek apakah user sedang login --}}
            @auth
                <li class="nav-item dropdown">
                    {{-- Gap-1 agar foto dan teks rapat --}}
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 pe-0" href="#" id="userDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">

                        {{-- FOTO PROFIL --}}
                        <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : asset('template-admin/img/avatars/profile.jpg') }}"
                            class="avatar img-fluid rounded-circle border border-2 border-success shadow-sm"
                            alt="{{ Auth::user()->name }}" style="width: 40px; height: 40px; object-fit: cover;">

                        {{-- NAMA & LEVEL --}}
                        <div class="d-none d-md-block text-start mt-2" style="line-height: 1.2;">
                            {{-- TAMPIL PENUH (Tanpa Limit) --}}
                            <span class="text-dark fw-semibold d-block">
                                {{ Auth::user()->name }}
                            </span>

                            <small class="text-muted" style="font-size: 0.75rem;">
                                {{ Auth::user()->level }}
                            </small>
                        </div>
                    </a>

                    {{-- DROPDOWN MENU --}}
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userDropdown">
                        {{-- Menu Profil --}}
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                                <i class="align-middle me-2" data-feather="user"></i> Profil Saya
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        {{-- Menu Logout --}}
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2 w-100 text-start">
                                    <i class="align-middle me-2" data-feather="log-out"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @else
                {{-- Jaga-jaga jika session hilang --}}
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3">Login</a>
                </li>
            @endauth

        </ul>
    </div>
</nav>
