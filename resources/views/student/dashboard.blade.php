@extends('layouts.app')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Dashboard Murid
                </h2>
            </div>
        </div>
    </div>
     <div class="mb-3">
    <label class="form-label d-block">Pilih Segment Tabungan</label>
    <div class="row row-cols-1 row-cols-md-4 g-3">
        @foreach ($joinedSegments as $segment)
            <div class="col">
                <a href="{{ route('student.segment.detail', $segment->id) }}" class="card h-100 text-decoration-none text-body">
                    <div class="position-relative overflow-hidden" style="padding-top: 35%;">
                        <img
                            src="{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}"
                            alt="Banner"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;"
                            class="card-img-top"
                        >
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-1">{{ $segment->name }}</h5>
                        <p class="card-text small text-muted">{{ $segment->description ?? 'Tidak ada deskripsi' }}</p>
                        <span class="badge bg-green-lt">Kode: {{ $segment->unique_code }}</span>
                    </div>
                </a>
            </div>
        @endforeach
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

    <div class="row row-cards">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Pengajuan Tabungan Menunggu Validasi</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Tanggal Pengajuan</th>
                                <th>Segment Tabungan</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $transaction->savingSegment->name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-warning-lt">Menunggu Validasi</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada pengajuan tabungan yang menunggu validasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Tabungan (Disetujui)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Tanggal Disetujui</th>
                                <th>Segment Tabungan</th>
                                <th>Jumlah</th>
                                <th>Disetujui Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($approvedTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->updated_at->format('d M Y H:i') }}</td>
                                    <td>{{ $transaction->savingSegment->name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>{{ $transaction->approver->name ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada tabungan yang disetujui.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pengajuan Ditolak</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Tanggal Ditolak</th>
                                <th>Segment Tabungan</th>
                                <th>Jumlah</th>
                                <th>Ditolak Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rejectedTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->updated_at->format('d M Y H:i') }}</td>
                                    <td>{{ $transaction->savingSegment->name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>{{ $segment->user->name ?? 'Pengguna Tidak Dikenal' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada pengajuan tabungan yang ditolak.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection