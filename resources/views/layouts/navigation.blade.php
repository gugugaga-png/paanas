<header class="navbar navbar-expand-md d-print-none">
  <div class="container-xl">
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbar-menu"
      aria-controls="navbar-menu"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <a href="{{ url('/') }}" aria-label="{{ config('app.name', 'Laravel') }}" class="navbar-brand navbar-brand-autodark me-3">
      <img src="{{ asset('images/logo.svg') }}" width="110" height="32" alt="{{ config('app.name', 'Laravel') }}" class="navbar-brand-image">
    </a>
    <div class="navbar-nav flex-row order-md-last ms-auto">
      @auth
      <div class="nav-item dropdown">
        <a
          href="#"
          class="nav-link d-flex lh-1 text-reset"
          data-bs-toggle="dropdown"
          aria-label="Open user menu"
        >
          <span class="avatar avatar-sm" style="overflow: hidden; display: inline-block;">
            <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default.svg') }}"
                 alt="Gambar Profil {{ Auth::user()->name }}"
                 style="width: 100%; height: 100%; object-fit: cover;">
          </span>
          <div class="d-none d-xl-block ps-2">
            <div>{{ Auth::user()->name }}</div>
            {{-- FIX: Access the 'name' property of the role object --}}
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
      <div class="nav-item d-none d-md-flex">
        <a class="btn" href="{{ route('login') }}">Login</a>
      </div>
      <div class="nav-item d-none d-md-flex">
        <a class="btn btn-primary ms-2" href="{{ route('register') }}">Register</a>
      </div>
      @endauth
    </div>
    <div class="collapse navbar-collapse" id="navbar-menu">
        <ul class="navbar-nav pt-lg-3">
            @auth
                {{-- Check role_id directly for Guru --}}
                @if (Auth::user()->role_id === 2)
                    <li class="nav-item @if(request()->routeIs('teacher.dashboard')) active @endif">
                        <a class="nav-link" href="{{ route('teacher.dashboard') }}">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 0 0 1 2 2v6" /></svg>
                            </span>
                            <span class="nav-link-title"> Dashboard Guru </span>
                        </a>
                    </li>
                    <li class="nav-item @if(request()->routeIs('teacher.segments.create')) active @endif">
                        <a class="nav-link" href="{{ route('teacher.segments.create') }}">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            </span>
                            <span class="nav-link-title"> Buat Segment </span>
                        </a>
                    </li>
                {{-- Use elseif for Murid --}}
                @elseif (Auth::user()->role_id === 3)
                    <li class="nav-item @if(request()->routeIs('student.dashboard')) active @endif">
                        <a class="nav-link" href="{{ route('student.dashboard') }}">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 0 0 1 2 2v6" /></svg>
                            </span>
                            <span class="nav-link-title"> Dashboard Murid </span>
                        </a>
                    </li>
                    <li class="nav-item @if(request()->routeIs('student.join_segment_form')) active @endif">
    <a class="nav-link" href="{{ route('student.join.segment.form') }}">
        <span class="nav-link-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12h-4" /><path d="M13 12h-3" /><path d="M8 7v10" /><path d="M20 6h-6a1 1 0 0 0 -1 1v10a1 1 0 0 0 1 1h6a1 1 0 0 0 1 -1v-10a1 1 0 0 0 -1 -1z" /></svg>
        </span>
        <span class="nav-link-title"> Gabung Segment </span>
    </a>
</li>
                    <li class="nav-item @if(request()->routeIs('student.deposit.form')) active @endif">
    <a class="nav-link" href="{{ route('student.deposit.form') }}">
        <span class="nav-link-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12h6" /><path d="M12 9v6" /></svg>
        </span>
        <span class="nav-link-title"> Tabung Dana </span>
    </a>
</li>
                @endif
            @endauth
        </ul>
    </div>
  </div>
</header>