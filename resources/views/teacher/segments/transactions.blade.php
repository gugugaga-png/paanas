{{-- resources/views/teacher/segments/transactions.blade.php --}}

@extends('layouts.app')

@section('title', 'Kelola Transaksi Segment')

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transaksi untuk Segment: <span class="text-blue">{{ $segment->name }}</span></h3>
            </div>
            <div class="card-body">
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0"/><path d="M5 12l6 6"/><path d="M5 12l6 -6"/></svg>
                    Kembali ke Dashboard Guru
                </a>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong class="me-2">Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong class="me-2">Gagal!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($transactions->isEmpty())
                    <div class="empty">
                        <div class="empty-img"><img src="https://raw.githubusercontent.com/tabler/tabler/dev/static/illustrations/undraw_printing_invoices_5i4r.svg" height="128" alt=""></div>
                        <p class="empty-title">Tidak ada transaksi ditemukan</p>
                        <p class="empty-subtitle text-muted">
                            Tidak ada transaksi yang tercatat untuk segmen ini.
                        </p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Jumlah</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Bukti Transfer</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                        <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                        <td><span class="badge bg-blue-lt">{{ ucfirst($transaction->type) }}</span></td>
                                        <td>
                                            @if($transaction->status == 'pending')
                                                <span class="badge bg-yellow-lt">Menunggu</span>
                                            @elseif($transaction->status == 'approved')
                                                <span class="badge bg-green-lt">Disetujui</span>
                                            @else
                                                <span class="badge bg-red-lt">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->proof_image_path)
                                                <a href="{{ Storage::url($transaction->proof_image_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Bukti</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->status == 'pending')
                                                <div class="d-flex gap-2">
                                                    <form action="{{ route('teacher.transactions.approve', $transaction) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                                    </form>
                                                    <form action="{{ route('teacher.transactions.reject', $transaction) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection