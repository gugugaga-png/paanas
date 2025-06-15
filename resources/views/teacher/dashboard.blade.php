@extends('layouts.app')

@section('content')

{{-- Modal untuk Membuat Segment Tabungan Baru --}}
<div class="modal fade" id="createSegmentModal" tabindex="-1" aria-labelledby="createSegmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> {{-- Menggunakan modal-lg untuk form yang lebih besar --}}
        <form action="{{ route('teacher.segments.store') }}" method="POST" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="createSegmentModalLabel">Buat Segment Tabungan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Segment</label>
                    <input type="text" name="name" class="form-control @error('name', 'createSegment') is-invalid @enderror" placeholder="Contoh: Tabungan Liburan" value="{{ old('name') }}" required>
                    @error('name', 'createSegment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control @error('description', 'createSegment') is-invalid @enderror" rows="3" placeholder="Deskripsi singkat tentang tujuan tabungan ini">{{ old('description') }}</textarea>
                    @error('description', 'createSegment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Target Jumlah (IDR)</label>
                    <input type="number" name="target_amount" class="form-control @error('target_amount', 'createSegment') is-invalid @enderror" placeholder="Contoh: 1000000" value="{{ old('target_amount') }}" min="0" required>
                    @error('target_amount', 'createSegment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                {{-- Anda mungkin ingin menambahkan input untuk unique_code di sini jika guru yang membuatnya --}}
                {{-- <div class="mb-3">
                    <label class="form-label">Kode Unik Segment (Opsional)</label>
                    <input type="text" name="unique_code" class="form-control @error('unique_code', 'createSegment') is-invalid @enderror" placeholder="Biarkan kosong untuk dibuat otomatis" value="{{ old('unique_code') }}">
                    @error('unique_code', 'createSegment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div> --}}
                <div class="mb-3">
                    <label for="banner" class="form-label">Banner Segmen</label>
                    <input type="file" class="form-control @error('banner', 'createSegment') is-invalid @enderror" id="banner" name="banner">
                    @error('banner', 'createSegment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Segment</button>
            </div>
        </form>
    </div>
</div>

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
                    {{-- Tombol yang akan membuka modal --}}
                    
                    
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

    <div class="row g-3"> {{-- Menggunakan g-3 untuk gap antar card --}}
        @forelse ($segments as $segment)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-5th"> {{-- Kolom yang disesuaikan --}}
                <a href="{{ route('teacher.segments.show', $segment) }}" class="text-decoration-none text-dark d-flex h-100">
                    <div class="my-3 card shadow-sm  overflow-hidden rounded-3 flex-fill" style="position: relative;">
                        {{-- Gambar banner --}}
                        <div class="position-relative" style="aspect-ratio: 3 / 1;">
                            <div class="w-100 h-100"
                                style="background-image: url('{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}');
                                        background-size: cover;
                                        background-position: center;">
                            </div>
                            <div class="position-absolute top-0 start-0 w-100 h-100 rounded-3" style="background-color: rgba(0, 0, 0, 0.12);"></div>
                            <div class="position-absolute top-0 start-0 text-white px-3 py-3 w-100">
                                <h4 class="fw-normal fs-2 mb-0">{{ $segment->name }}</h4>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="px-3 py-3 border-top" style="height: 145px;">
                            <div class="fw-normal fs-4 text-dark">{{ $segment->description }}</div>
                        </div>

                        {{-- Footer --}}
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
            <div class="col-12">
                <p class="text-center">Tidak ada segment tabungan yang dibuat.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-show modal if there are validation errors from segment creation
        @if ($errors->hasAny(['name', 'description', 'target_amount', 'banner'], 'createSegment'))
            var createSegmentModal = new bootstrap.Modal(document.getElementById('createSegmentModal'));
            createSegmentModal.show();
        @endif

        // Example for any other JS if needed (like for deposit/withdraw if they were on this page)
        // ...
    });
</script>
@endpush
