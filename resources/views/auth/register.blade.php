@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center bg-light">
    <div class="card shadow-sm border-0" style="width: 100%; max-width: 420px;">
        <div class="card-body p-4">
            {{-- Logo atau Judul --}}
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px;" onerror="this.style.display='none'">
                <h4 class="fw-bold mt-3">Buat Akun</h4>
                <p class="text-muted small">Daftar untuk mulai menggunakan aplikasi</p>
            </div>

            {{-- Form Register --}}
            <form method="POST" action="{{ route('register') }}" novalidate autocomplete="off">
                @csrf

                <div class="mb-3">
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap"
                        class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                        class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <input type="password" name="password" placeholder="Kata sandi"
                        class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi kata sandi"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="" disabled selected>-- Pilih Role --</option>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill py-2">Daftar</button>
                </div>
            </form>

            <div class="text-center mt-4 small text-muted">
                Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Login di sini</a>
            </div>
        </div>
    </div>
</div>
@endsection
