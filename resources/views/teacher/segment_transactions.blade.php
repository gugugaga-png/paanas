@extends('layouts.app')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Transaksi untuk Segment: {{ $segment->name }}
                </h2>
                <div class="text-muted mt-1">
                    Kode Unik: <span class="badge bg-green-lt">{{ $segment->unique_code }}</span>
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 8h1" /></svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Transaksi</h3>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Murid</th>
                        <th>Jumlah Tabungan</th>
                        <th>Tipe Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $transaction->user->name }}</td> {{-- Access the user's name via the relationship --}}
                            <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $transaction->type == 'deposit' ? 'green' : 'red' }}-lt">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada transaksi untuk segmen ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection