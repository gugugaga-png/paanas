<aside class="navbar navbar-vertical navbar-expand-sm position-absolute" data-bs-theme="light">
    <div class="container-fluid">
        <button
  class="navbar-toggler"
  type="button"
  data-bs-toggle="offcanvas"
  data-bs-target="#offcanvas-sidebar"
  aria-controls="offcanvas-sidebar"
  aria-label="Toggle sidebar"
>
  <span class="navbar-toggler-icon"></span>
</button>

        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" width="110" height="32" alt="{{ config('app.name', 'Laravel') }}" class="navbar-brand-image">
            </a>
        </h1>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">

                {{-- Home Link: Always visible for all users --}}
                @guest
                    <li class="nav-item {{ Request::routeIs('home') || Request::is('/') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/></svg>
                            </span>
                            <span class="nav-link-title"> Home </span>
                        </a>
                    </li>
                @endguest


                {{-- Navigation for Authenticated Users (Teacher and Student) --}}
                @auth
                    {{-- Dashboard Link (redirects based on role) --}}
                    <li class="nav-item {{ Request::routeIs('dashboard') || Request::routeIs('teacher.dashboard') || Request::routeIs('student.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 13v9"/><path d="M3 13h18"/><path d="M13 3h4l-1 7h-3z"/><path d="M7 3h4l-1 7h-3z"/></svg>
                            </span>
                            <span class="nav-link-title"> Dashboard </span>
                        </a>
                    </li>

                    {{-- Profile Link --}}
                    {{-- Ensure this routes to profile.show or profile.edit as per your profile setup --}}
                    <li class="nav-item {{ Request::routeIs('profile.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('profile.show') }}"> {{-- Changed to profile.show based on previous discussion --}}
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>
                            </span>
                            <span class="nav-link-title"> Profile </span>
                        </a>
                    </li>

                    {{-- Segment Tabungan Dropdown --}}
                    @php
                        $user = Auth::user();
                        $segments = $user->role_id === 2
                            ? \App\Models\SavingSegment::where('user_id', $user->id)->get()
                            : ($user->role_id === 3
                                ? $user->segments
                                : collect());

                        // Check if any segment route is active to keep dropdown open
                        $isAnySegmentRouteActive = Request::routeIs('teacher.segments.show'); // Adjust if student segments have a similar route
                        if ($user->role_id === 3 && $segments->isNotEmpty()) { // If student, check if any joined segment is currently active
                            foreach ($segments as $segment) {
                                if (request()->segment(count(request()->segments())) == $segment->id && Request::routeIs('student.segments.show', $segment)) { // Assuming student segment show route
                                    $isAnySegmentRouteActive = true;
                                    break;
                                }
                            }
                        }
                    @endphp

                    @if ($segments->count())
                        {{-- Add 'active' and 'show' classes conditionally --}}
                        <li class="nav-item dropdown {{ $isAnySegmentRouteActive ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle {{ $isAnySegmentRouteActive ? 'show' : '' }}" href="#navbar-segments" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ $isAnySegmentRouteActive ? 'true' : 'false' }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/></svg>
                                </span>
                                <span class="nav-link-title">Segment Tabungan</span>
                            </a>
                            <div class="dropdown-menu {{ $isAnySegmentRouteActive ? 'show' : '' }}" id="navbar-segments">
                                @foreach ($segments as $segment)
                                    {{-- Highlight active segment item --}}
                                    @if(Auth::user()->role_id === 2)
                                        <a href="{{ route('teacher.segments.show', $segment) }}"
                                           class="dropdown-item {{ Request::routeIs('teacher.segments.show') && request()->segment(count(request()->segments())) == $segment->id ? 'active' : '' }}">
                                            {{ $segment->name }}
                                        </a>
                                    @elseif(Auth::user()->role_id === 3)
                                        <a href="{{ route('student.segments.show', $segment) }}"
                                           class="dropdown-item {{ Request::routeIs('student.segments.show') && request()->segment(count(request()->segments())) == $segment->id ? 'active' : '' }}">
                                            {{ $segment->name }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                    @endif

                    {{-- Teacher Specific Navigation --}}
                    @if(Auth::user()->role_id === 2) {{-- Assuming 2 is the role_id for Teacher --}}
                        <li class="nav-item {{ Request::routeIs('teacher.mail.index') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('teacher.mail.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12l-9 -9"/><path d="M12 12l9 -9"/><path d="M12 12l6 6l-3 3"/><path d="M12 12l-6 6l3 3"/><path d="M19 12h2a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1h2"/></svg>
                                </span>
                                <span class="nav-link-title"> Inbox (Transactions) </span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::routeIs('teacher.segments.create') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('teacher.segments.create') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                </span>
                                <span class="nav-link-title"> Create Segment </span>
                            </a>
                        </li>
                    @endif

                @endauth

                {{-- Login/Register Links for Guests --}}
                @guest
                    <li class="nav-item {{ Request::routeIs('login') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('login') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M20 12h-13l3 -3m0 6l-3 -3"/></svg>
                            </span>
                            <span class="nav-link-title"> Login </span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('register') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('register') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.12a3 3 0 1 1 0 5.76a3 3 0 0 1 0 -5.76"/></svg>
                            </span>
                            <span class="nav-link-title"> Register </span>
                        </a>
                    </li>
                @endguest

            </ul>
        </div>
    </div>
</aside>