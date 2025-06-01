{{-- resources/views/teacher/mail/index.blade.php --}}

@extends('layouts.app') {{-- Or your main layout --}}

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    @if(isset($savingSegment) && $savingSegment)
                        Pengajuan Transaksi Segmen: {{ $savingSegment->name }}
                    @else
                        Kotak Masuk Transaksi (Semua Segmen)
                    @endif
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @if(isset($savingSegment) && $savingSegment)
                        <a href="{{ route('teacher.mail.index') }}" class="btn d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-narrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l4 4" /><path d="M5 12l4 -4" /></svg>
                            Lihat Semua Transaksi
                        </a>
                    @else
                        <a href="{{ route('teacher.dashboard') }}" class="btn d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-narrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l4 4" /><path d="M5 12l4 -4" /></svg>
                            Kembali ke Dashboard
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Pesan Sukses/Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Pengajuan Transaksi Menunggu Validasi</h3>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <th>Murid</th>
                        @if(!isset($savingSegment) || !$savingSegment) {{-- Tampilkan kolom Segmen hanya jika melihat semua transaksi --}}
                            <th>Segmen</th>
                        @endif
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                            @if(!isset($savingSegment) || !$savingSegment)
                                <td>{{ $transaction->savingSegment->name ?? 'N/A' }} <span class="badge bg-green-lt">{{ $transaction->savingSegment->unique_code ?? '' }}</span></td>
                            @endif
                            <td>
                                @if($transaction->type == 'deposit')
                                    <span class="badge bg-green-lt">Setoran</span>
                                @else
                                    <span class="badge bg-red-lt">Penarikan</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('teacher.transactions.approve', $transaction) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                </form>
                                <form action="{{ route('teacher.transactions.reject', $transaction) }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Tolak</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (isset($savingSegment) && $savingSegment) ? '5' : '6' }}" class="text-center">Tidak ada pengajuan transaksi yang menunggu validasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection