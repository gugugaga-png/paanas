@extends('layouts.app')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Dashboard Guru
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('teacher.segments.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Buat Segment Tabungan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 row-cols-lg-4 row-cols-xl-4  g-4">
    @forelse ($segments as $segment)
        <div class="col">
            <div class="card d-flex flex-column h-100"> {{-- Tambahkan h-100 untuk tinggi kartu yang seragam --}}
            <a href="#">
    @if($segment->banner)
        <div class="banner-container" style="position: relative; width: 100%; padding-top: 35%; overflow: hidden;">
            <img src="{{ asset('storage/' . $segment->banner) }}" alt="Segment Banner" class="banner-image"
                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
        </div>
    @else
        <div class="banner-container" style="position: relative; width: 100%; padding-top: 35%; overflow: hidden;">
            <img src="{{ asset('images/default.png') }}" alt="Default Banner" class="banner-image"
                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
        </div>
    @endif
</a>
                <div class="card-body d-flex flex-column">
                    <h3 class="card-title">
                        <h3 class="card-title">
    <a href="{{ route('teacher.segments.show', $segment) }}">{{ $segment->name }}</a>
</h3>
                    </h3>
                    <div class="text-secondary flex-grow-1"> {{-- flex-grow-1 agar deskripsi mengambil ruang dan mendorong footer ke bawah --}}
                        {{ $segment->description ?? 'Tidak ada deskripsi' }}
                    </div>
                    <div class="d-flex align-items-center pt-4 mt-auto">
                        {{-- Memanggil avatar pembuat segment (pastikan $segment->user->avatar_url tersedia) --}}
                        <span class="avatar me-2" style="background-image: url({{ $segment->user->avatar_url ?? '/static/avatars/default.jpg' }})"></span>
                        <div class="ms-1">
                            {{-- Memanggil nama pembuat segment --}}
                            <a href="#" class="text-body">{{ $segment->user->name ?? 'Pengguna Tidak Dikenal' }}</a>
                            {{-- Tampilkan kapan segment dibuat --}}
                            <div class="text-secondary">{{ $segment->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="ms-auto">
                            <a href="#" class="icon d-none d-md-inline-block ms-3 text-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"><path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" /></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p class="text-center">Belum ada segmen tabungan yang dibuat.</p>
        </div>
    @endforelse
</div>
    </div>
   
</div>
@endsection