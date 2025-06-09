@extends('layouts.app') {{-- Ganti dengan layout utama aplikasi Anda --}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Profil Pengguna</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- Penting: Gunakan metode PUT untuk update --}}

                        <div class="mb-3 text-center">
                            @if ($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Gambar Profil" class="img-thumbnail rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/default.svg') }}" alt="Gambar Profil Default" class="img-thumbnail rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover;">
                            @endif
                            <label for="profile_picture" class="form-label d-block mt-2">Ganti Gambar Profil</label>
                            <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture" accept="image/*">
                            @error('profile_picture')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio (Opsional)</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection