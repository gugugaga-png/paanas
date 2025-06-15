@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center bg-light">
    <div class="card shadow-sm border-0" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            {{-- Logo / Judul --}}
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px;" onerror="this.style.display='none'">
                <h4 class="fw-bold mt-3">Masuk</h4>
                <p class="text-muted small">Gunakan akun Anda untuk login</p>
            </div>

            {{-- Alert status jika ada --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Form Login --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <input type="email" name="email" placeholder="Email"
                        class="form-control @error('email') is-invalid @enderror" required autofocus
                        value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <input type="password" name="password" placeholder="Password"
                        class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill">Login</button>
            </form>

            {{-- Register link --}}
            <div class="text-center mt-4">
                <p class="small text-muted mb-0">Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Daftar</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
