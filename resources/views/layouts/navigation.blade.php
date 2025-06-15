<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        {{-- Logo (Terlihat oleh semua, link ke Home) --}}
        @guest
    <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/logo.svg') }}" width="110" height="32" alt="Shapphy Logo" class="navbar-brand-image">
        </a>
    </h1>
@endguest


        {{-- Tombol Toggler untuk Offcanvas (Hanya di Mobile) --}}
        <button
            class="navbar-toggler d-md-none"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasMainSidebar"
            aria-controls="offcanvasMainSidebar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-nav flex-row order-md-last ms-auto">
            {{-- Tombol "Gabung Segment" hanya untuk siswa di desktop (di samping profil) --}}
            @if (Auth::check() && Auth::user()->role_id === 3 && request()->routeIs('student.dashboard'))
                <div class="nav-item d-none d-md-flex align-items-center me-2">
                    <button type="button" class="btn btn-icon btn-light" data-bs-toggle="modal" data-bs-target="#joninsegmentModal" title="Gabung Segment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Tombol "Buat Segment" hanya untuk guru di desktop (di samping profil) --}}
            @if (Auth::check() && Auth::user()->role_id === 2 && request()->routeIs('teacher.dashboard'))
                <div class="nav-item d-none d-md-flex align-items-center me-2">
                    <button type="button" class="btn btn-icon btn-light" data-bs-toggle="modal" data-bs-target="#createSegmentModal" title="Buat Segment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" />
                        </svg>
                    </button>
                </div>
            @endif

            @auth
                {{-- Dropdown Profil Pengguna (Hanya jika sudah login) --}}
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset" data-bs-toggle="dropdown" aria-label="Open user menu">
                        <span class="avatar avatar-sm" style="overflow: hidden; display: inline-block;">
                            <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default.svg') }}"
                                alt="Gambar Profil {{ Auth::user()->name }}"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="mt-1 small text-secondary">{{ Auth::user()->role->name ?? 'N/A' }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ route('profile.editprofile') }}" class="dropdown-item">Profile</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Link Login/Register untuk tamu (TERLIHAT DI SEMUA UKURAN LAYAR) --}}
                {{-- Menggunakan d-flex agar selalu terlihat, ms-2 untuk margin --}}
                <div class="nav-item d-flex">
                    <a class="btn" href="{{ route('login') }}">Login</a>
                </div>
                <div class="nav-item d-flex">
                    <a class="btn btn-primary ms-2" href="{{ route('register') }}">Register</a>
                </div>
            @endauth
        </div>

        {{-- Menu Navigasi Utama (Desktop Only) --}}
        {{-- Bagian ini hanya akan terlihat ketika pengguna sudah login --}}
        <div class="collapse navbar-collapse d-none d-md-flex" id="navbar-menu">
            <ul class="navbar-nav pt-lg-3">
                @auth
                    {{-- Tampilkan menu ini HANYA jika pengguna BUKAN GURU (role_id === 2) --}}
                    @if (Auth::user()->role_id !== 2)
                        {{-- Link Home untuk semua pengguna (jika bukan guru) --}}
                        <li class="nav-item {{ Request::routeIs('home') || Request::is('/') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('/') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/>
                                    </svg>
                                </span>
                                <span class="nav-link-title">Home</span>
                            </a>
                        </li>

                        {{-- Dashboard Link (untuk siswa/admin/non-guru) --}}
                        <li class="nav-item {{ Request::routeIs('dashboard') || Request::routeIs('student.dashboard') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 13v9"/><path d="M3 13h18"/><path d="M13 3h4l-1 7h-3z"/><path d="M7 3h4l-1 7h-3z"/>
                                    </svg>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        {{-- Profile Link (untuk siswa/admin/non-guru) --}}
                        <li class="nav-item {{ Request::routeIs('profile.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('profile.show') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                    </svg>
                                </span>
                                <span class="nav-link-title">Profile</span>
                            </a>
                        </li>

                        {{-- Segment Tabungan Dropdown (untuk siswa/admin/non-guru) --}}
                        @php
                            $user = Auth::user();
                            $segments = collect();
                            if ($user->role_id === 3) { // Hanya ambil segmen jika siswa
                                $segments = $user->segments;
                            }
                            // Logika untuk aktifkan dropdown
                            $isAnySegmentRouteActive = false;
                            if ($user->role_id === 3 && $segments->isNotEmpty()) {
                                foreach ($segments as $segment) {
                                    if (request()->segment(count(request()->segments())) == $segment->id && Request::routeIs('student.segments.show', $segment)) {
                                        $isAnySegmentRouteActive = true;
                                        break;
                                    }
                                }
                            }
                        @endphp

                        @if ($segments->count())
                            <li class="nav-item dropdown {{ $isAnySegmentRouteActive ? 'active' : '' }}">
                                <a class="nav-link dropdown-toggle {{ $isAnySegmentRouteActive ? 'show' : '' }}" href="#navbar-segments" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ $isAnySegmentRouteActive ? 'true' : 'false' }}">
                                    <span class="nav-link-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Segment Tabungan</span>
                                </a>
                                <div class="dropdown-menu {{ $isAnySegmentRouteActive ? 'show' : '' }}" id="navbar-segments">
                                    @foreach ($segments as $segment)
                                        <a href="{{ route('student.segments.show', $segment) }}"
                                           class="dropdown-item {{ Request::routeIs('student.segments.show') && request()->segment(count(request()->segments())) == $segment->id ? 'active' : '' }}">
                                            {{ $segment->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </li>
                        @endif
                    @endif {{-- End if not teacher --}}
                @endauth
            </ul>
        </div>
    </div>
</header>

{{-- Offcanvas Sidebar (Mobile Only) --}}
{{-- Bagian ini diubah untuk menyertakan Login/Register untuk tamu --}}
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="offcanvasMainSidebar" aria-labelledby="offcanvasMainSidebarLabel">
    <div class="offcanvas-header">
        {{-- Logo di Offcanvas (Terlihat oleh semua, link ke Home) --}}
        <h5 class="offcanvas-title" id="offcanvasMainSidebarLabel">
            <a href="{{ url('/') }}" class="text-decoration-none text-reset">
                <img src="{{ asset('images/logo.svg') }}" width="110" height="32" alt="Shapphy Logo" class="d-inline-block align-text-bottom me-2">
               
            </a>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav pt-lg-3">
            {{-- Link Home untuk semua pengguna (tetap di sini untuk offcanvas) --}}
            <li class="nav-item {{ Request::routeIs('home') || Request::is('/') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/') }}">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/>
                        </svg>
                    </span>
                    <span class="nav-link-title">Home</span>
                </a>
            </li>

            @auth
                @php
                    $user = Auth::user();
                @endphp

                {{-- Dashboard Link (Menampilkan Dashboard Umum untuk Siswa/Admin, Dashboard Guru untuk Guru) --}}
                @if ($user->role_id === 2)
                    <li class="nav-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('teacher.dashboard') }}">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 13v9"/><path d="M3 13h18"/><path d="M13 3h4l-1 7h-3z"/><path d="M7 3h4l-1 7h-3z"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Dashboard Guru</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item {{ Request::routeIs('dashboard') || Request::routeIs('student.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 13v9"/><path d="M3 13h18"/><path d="M13 3h4l-1 7h-3z"/><path d="M7 3h4l-1 7h-3z"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>
                @endif

                {{-- Profile Link (untuk semua pengguna yang login) --}}
                <li class="nav-item {{ Request::routeIs('profile.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('profile.show') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Profile</span>
                    </a>
                </li>

                {{-- Segment Tabungan Dropdown (untuk semua pengguna yang login) --}}
                @php
                    $segments = collect();
                    if ($user->role_id === 2) { // Guru
                        $segments = \App\Models\SavingSegment::where('user_id', $user->id)->get();
                    } elseif ($user->role_id === 3) { // Siswa
                        $segments = $user->segments;
                    }

                    $isAnySegmentRouteActive = false;
                    if ($user->role_id === 2) {
                        $isAnySegmentRouteActive = Request::routeIs('teacher.segments.show');
                    } elseif ($user->role_id === 3 && $segments->isNotEmpty()) {
                        foreach ($segments as $segment) {
                            if (request()->segment(count(request()->segments())) == $segment->id && Request::routeIs('student.segments.show', $segment)) {
                                $isAnySegmentRouteActive = true;
                                break;
                            }
                        }
                    }
                @endphp

                @if ($segments->count())
                    <li class="nav-item dropdown {{ $isAnySegmentRouteActive ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle {{ $isAnySegmentRouteActive ? 'show' : '' }}" href="#navbar-segments" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ $isAnySegmentRouteActive ? 'true' : 'false' }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Segment Tabungan</span>
                        </a>
                        <div class="dropdown-menu {{ $isAnySegmentRouteActive ? 'show' : '' }}" id="navbar-segments">
                            @foreach ($segments as $segment)
                                @if($user->role_id === 2)
                                    <a href="{{ route('teacher.segments.show', $segment) }}"
                                       class="dropdown-item {{ Request::routeIs('teacher.segments.show') && request()->segment(count(request()->segments())) == $segment->id ? 'active' : '' }}">
                                        {{ $segment->name }}
                                    </a>
                                @elseif($user->role_id === 3)
                                    <a href="{{ route('student.segments.show', $segment) }}"
                                       class="dropdown-item {{ Request::routeIs('student.segments.show') && request()->segment(count(request()->segments())) == $segment->id ? 'active' : '' }}">
                                        {{ $segment->name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </li>
                @endif

                {{-- Navigasi Khusus Guru (hanya tambahan jika peran guru) --}}
                @if($user->role_id === 2)
                    {{-- Buat Segment (Link di offcanvas) --}}
                    <li class="nav-item {{ request()->routeIs('teacher.segments.create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('teacher.segments.create') }}">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">Buat Segment</span>
                        </a>
                    </li>
                    {{-- Mail Guru --}}
                    <li class="nav-item {{ Request::routeIs('teacher.mail.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('teacher.mail.index') }}">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 0 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" />
                                </svg>
                            </span>
                            <span class="nav-link-title">Mail Guru</span>
                        </a>
                    </li>
                @endif
            @else
                {{-- Link Home untuk Guest di Offcanvas --}}
                <li class="nav-item {{ Request::routeIs('home') || Request::is('/') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Home</span>
                    </a>
                </li>
                {{-- Jika belum login, tampilkan link Login dan Register di offcanvas --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                </li>
            @endauth
        </ul>
    </div>
</div>