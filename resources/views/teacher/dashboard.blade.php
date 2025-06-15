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
    <div class="row ">
    @forelse ($segments as $segment)
        <div class="col-12 col-md-3 col-xl-5th">
            <a href="{{ route('teacher.segments.show', $segment) }}" class="text-decoration-none text-dark">
                <div class="card shadow-sm border-0 overflow-hidden rounded-3 h-100" style="position: relative;">
                    {{-- Gambar banner --}}
                    <div class="position-relative" style="aspect-ratio: 3 / 1;">
                        <div class="w-100 h-100"
                            style="background-image: url('{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}');
                                background-size: cover;
                                background-position: center;">
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(0, 0, 0, 0.12);"></div>
                        <div class="position-absolute top-0 start-0 text-white px-3 py-3 w-100">
                            <h4 class="fw-normal fs-2 mb-0">{{ $segment->name }}</h4>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="px-3 py-3 border-top" style="height: 145px;">
                        <div class="fw-normal    fs-4 text-dark">{{ $segment->description }}</div>
                 </div>

                   <div class="card-footer">
                    @if($segment->totalTarget > 0)
    @php
        $percent = min(100, round(($segment->currentBalance / $segment->totalTarget) * 100, 1));
    @endphp
    <div class="p">
        <div class="d-flex justify-content-between">
            <span class="text-muted small">Progress Tabungan</span>
            <strong class="small">{{ $percent }}%</strong>
        </div>
        <div class="progress mt-1" style="height: 0.5rem;">
            <div class="progress-bar bg-primary" role="progressbar"
                 style="width: {{ $percent }}%;"
                 aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>
        <div class="text-muted small mt-1">
            Rp{{ number_format($segment->currentBalance, 0, ',', '.') }} dari Rp{{ number_format($segment->totalTarget, 0, ',', '.') }}
        </div>
    </div>
@endif
                   </div>


                    <span class="stretched-link"></span>
                </div>
            </a>
        </div>
    @empty
        <p class="text-center">Tidak ada segment yang diikuti.</p>
    @endforelse
</div>
    </div>
   
</div>
@endsection