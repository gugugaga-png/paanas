<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button
            class="navbar-toggler d-md-none" {{-- Sembunyikan di desktop --}}
            type="button"
            data-bs-toggle="offcanvas" {{-- Gunakan offcanvas Bootstrap --}}
            data-bs-target="#offcanvasMainSidebar"
            aria-controls="offcanvasMainSidebar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        

        <div class="navbar-nav flex-row order-md-last ms-auto">
            {{-- Tombol "Gabung Segment" hanya untuk siswa di desktop --}}
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

            @auth
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
                {{-- Link Login/Register hanya di desktop --}}
                <div class="nav-item d-none d-md-flex">
                    <a class="btn" href="{{ route('login') }}">Login</a>
                </div>
                <div class="nav-item d-none d-md-flex">
                    <a class="btn btn-primary ms-2" href="{{ route('register') }}">Register</a>
                </div>
            @endauth
        </div>

        <div class="collapse navbar-collapse d-none d-md-flex" id="navbar-menu">
            <ul class="navbar-nav pt-lg-3">
                @auth
                    @if (Auth::user()->role_id === 2)
                        <li class="nav-item @if(request()->routeIs('teacher.dashboard')) active @endif">
                            <a class="nav-link" href="{{ route('teacher.dashboard') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                        <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 0 0 1 2 2v6" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">Dashboard Guru</span>
                            </a>
                        </li>
                        <li class="nav-item @if(request()->routeIs('teacher.segments.create')) active @endif">
                            <a class="nav-link" href="{{ route('teacher.segments.create') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">Buat Segment</span>
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</header>

<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="offcanvasMainSidebar" aria-labelledby="offcanvasMainSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasMainSidebarLabel">Menu Navigasi</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav pt-lg-3">
            {{-- Link Home untuk semua pengguna --}}
            <li class="nav-item {{ Request::routeIs('home') || Request::is('/') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/') }}">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l-2 0l9 -9l9 9l-2 0"/>
                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                            <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 0 0 1 2 2v6"/>
                        </svg>
                    </span>
                    <span class="nav-link-title">Home</span>
                </a>
            </li>

            @auth
                {{-- Dashboard Link --}}
                <li class="nav-item {{ Request::routeIs('dashboard') || Request::routeIs('teacher.dashboard') || Request::routeIs('student.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 13v9"/>
                                <path d="M3 13h18"/>
                                <path d="M13 3h4l-1 7h-3z"/>
                                <path d="M7 3h4l-1 7h-3z"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                {{-- Profile Link --}}
                <li class="nav-item {{ Request::routeIs('profile.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('profile.show') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Profile</span>
                    </a>
                </li>

                {{-- Segment Tabungan Dropdown --}}
                @php
                    $user = Auth::user();
                    $segments = collect();

                    if ($user->role_id === 2) {
                        $segments = \App\Models\SavingSegment::where('user_id', $user->id)->get();
                    } elseif ($user->role_id === 3) {
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
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/>
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

                {{-- Navigasi Khusus Guru --}}
                @if($user->role_id === 2)
                    <li class="nav-item {{ Request::routeIs('teacher.mail.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('teacher.mail.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 12l-9 -9"/>
                                    <path d="M12 12l9 -9"/>
                                    <path d="M12 12l6 6l-3 3"/>
                                    <path d="M12 12l-6 6l3 3"/>
                                    <path d="M19 12h2a1 1 0 0 1 1 1v5a2 2 0 0 1 -2 2h-6a1 1 0 0 1 -1 -1v-2"/>
                                    <path d="M5 12h-2a1 1 0 0 0 -1 1v5a2 2 0 0 0 2 2h6a1 1 0 0 0 1 -1v-2"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Mail Guru</span>
                        </a>
                    </li>
                @endif
            @else
                {{-- Jika belum login, tampilkan link Login dan Register --}}
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
