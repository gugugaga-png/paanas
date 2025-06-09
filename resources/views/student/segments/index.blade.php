@extends('layouts.app')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Semua Segmen Tabungan Anda
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button type="button" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#joninsegmentModal">
                        <i class="ti ti-plus me-2"></i>Gabung Segment Baru
                    </button>
                    <button type="button" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#joninsegmentModal" aria-label="Gabung Segment Baru">
                        <i class="ti ti-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="list-group list-group-flush">
            @forelse ($allJoinedSegments as $segment)
                <a href="{{ route('student.segment.detail', $segment->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}"
                            alt="Banner" class="avatar avatar-md rounded-circle" style="object-fit: cover;">
                        <div>
                            <strong>{{ $segment->name }}</strong>
                            <div class="text-muted text-small">{{ $segment->description }}</div>
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-green-lt text-uppercase ms-auto">Rp {{ number_format($segment->balance, 0, ',', '.') }}</span>
                    </div>
                </a>
            @empty
                <div class="list-group-item text-center text-muted">Anda belum bergabung dengan segmen tabungan manapun.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection