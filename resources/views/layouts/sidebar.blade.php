<aside class="navbar navbar-vertical navbar-expand-sm position-absolute" data-bs-theme="light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ url('/') }}">
                <img src="/static/logo-white.svg" width="110" height="32" alt="Tabler" class="navbar-brand-image" />
            </a>
        </h1>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">

                {{-- Always visible for all users (including guests) --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">
                        <span class="nav-link-title"> Home </span>
                    </a>
                </li>

                {{-- Navigation for Authenticated Users (Teacher and Student) --}}
                @auth
                    {{-- Dashboard Link (redirects based on role) --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-title"> Dashboard </span>
                        </a>
                    </li>

                    {{-- Profile Link --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.edit') }}">
                            <span class="nav-link-title"> Profile </span>
                        </a>
                    </li>
                    {{-- Segment Tabungan Dropdown --}}
                    
@auth
    @php
        // Ambil segment berdasarkan peran user
        $user = Auth::user();
        $segments = $user->role_id === 2
            ? \App\Models\SavingSegment::where('user_id', $user->id)->get()
            : ($user->role_id === 3
                ? $user->segments // relasi many-to-many siswa <-> segment
                : collect());
    @endphp

    @if ($segments->count())
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <span class="nav-link-title">Segment Tabungan</span>
            </a>
            <div class="dropdown-menu">
                @foreach ($segments as $segment)
                    <a href="{{ route('student.segment.detail', $segment->id) }}" class="dropdown-item">
                        {{ $segment->name }}
                    </a>
                @endforeach
            </div>
        </li>
    @endif
@endauth


                    {{-- Teacher Specific Navigation --}}
                    @if(Auth::user()->role_id === 2) {{-- Assuming 2 is the role_id for Teacher --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('teacher.mail.index') }}">
                                <span class="nav-link-title"> Inbox (Transactions) </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('teacher.segments.create') }}">
                                <span class="nav-link-title"> Create Segment </span>
                            </a>
                        </li>
                        {{-- You might want a link to view all segments, if such a route exists --}}
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('teacher.segments.index') }}">
                                <span class="nav-link-title"> My Segments </span>
                            </a>
                        </li> --}}
                    @endif

                    {{-- Student Specific Navigation --}}
                    @if(Auth::user()->role_id === 3) {{-- Assuming 3 is the role_id for Student --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.join.segment.form') }}">
    <span class="nav-link-title"> Join Segment </span>
</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.deposit.form') }}">
                                <span class="nav-link-title"> Deposit </span>
                            </a>
                        </li>
                    @endif
                @endauth

                {{-- Login/Register Links for Guests --}}
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <span class="nav-link-title"> Login </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <span class="nav-link-title"> Register </span>
                        </a>
                    </li>
                @endguest

            </ul>
        </div>
    </div>
</aside>