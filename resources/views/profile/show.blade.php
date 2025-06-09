@extends('layouts.app') {{-- Ganti dengan layout utama aplikasi Anda --}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Profil Pengguna</div>

                <div class="card-body text-center">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        @if ($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Gambar Profil" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{ asset('images/default.svg') }}" alt="Gambar Profil Default" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                    </div>

                    <h2>{{ $user->name }}</h2>
                    <p class="text-muted">{{ $user->email }}</p>

                    @if ($user->bio)
                        <p><strong>Bio:</strong> {{ $user->bio }}</p>
                    @else
                        <p class="text-muted">Bio belum diisi.</p>
                    @endif

                    <a href="{{ route('profile.editprofile') }}" class="btn btn-primary mt-3">Edit Profil</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection