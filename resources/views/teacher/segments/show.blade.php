@extends('layouts.app') {{-- Adjust this if your layout is different --}}

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Detail Segment: {{ $segment->name }}
                </h2>
                <div class="text-secondary mt-1">Kode Unik: <span class="badge bg-green-lt">{{ $segment->unique_code }}</span></div>
                <div class="text-secondary">{{ $segment->description ?? 'Tidak ada deskripsi.' }}</div>
                <div class="text-secondary">Dibuat oleh: {{ $segment->user->name ?? 'Tidak diketahui' }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" /></svg>
                        Kembali ke Dashboard
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
    {{-- banner
     --}}
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
    {{-- Tombol untuk melihat Data Siswa --}}
    <div class="mb-3 mt-4">
        <a href="{{ route('teacher.segments.students', $segment) }}" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
            Lihat Data Siswa di Segment Ini
        </a>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Riwayat Transaksi Segment Ini</h3>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Murid</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($transaction->type == 'deposit')
                                    <span class="badge bg-green-lt">Setoran</span>
                                @else
                                    <span class="badge bg-red-lt">Penarikan</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($transaction->status == 'pending')
                                    <span class="badge bg-yellow-lt">Menunggu</span>
                                @elseif($transaction->status == 'approved')
                                    <span class="badge bg-green-lt">Disetujui</span>
                                @else
                                    <span class="badge bg-red-lt">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada riwayat transaksi untuk segmen ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
</div>
@endsection