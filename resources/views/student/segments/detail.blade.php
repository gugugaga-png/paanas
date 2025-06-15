@extends('layouts.app')

@section('content')

{{-- Modal Withdraw --}}
<div class="modal modal-blur fade" id="withdrawModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('student.withdraw.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="saving_segment_id" value="{{ $segment->id }}">
            <div class="modal-header">
                <h5 class="modal-title">Ajukan Penarikan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="withdraw_amount" class="form-label">Jumlah Penarikan (IDR)</label>
                    <input
                        type="number"
                        name="amount"
                        id="withdraw_amount"
                        class="form-control @error('amount') is-invalid @enderror"
                        min="1000"
                        placeholder="Contoh: 25000"
                        required
                        value="{{ old('amount') }}">
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Ajukan</button>
            </div>
        </form>
    </div>
</div>

<div class="container-xl">
    {{-- Full Width Banner for Segment Info --}}
    <div class="card card-cover card-status-top-orange mb-4 rounded-3"
    style="background-image: url('{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}'); background-size: cover; background-position: center; min-height: 250px; position: relative;">

        <div style="
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 20%);
            border-radius: inherit;
        "></div>

        <div class="card-body d-flex flex-column justify-content-end align-items-start text-white"
            style="position: absolute; bottom: 20px; left: 20px; right: 20px; z-index: 2; text-align: left;">

            <h1 class="display-4 fw-bold mb-2">{{ $segment->name }}</h1>
            <p class="mb-3">{{ $segment->description ?? 'Tidak ada deskripsi' }}</p>
        </div>
    </div>

    <div class="row g-4 mb-4"> {{-- Menggunakan g-4 untuk gap antar kolom dan mb-4 untuk margin bawah --}}
        {{-- Kolom Kiri (Saldo dan Statistik) --}}
        <div class="col-12 col-lg-5 d-flex flex-column"> {{-- Tetap col-12 untuk mobile, col-lg-5 untuk layar besar, d-flex flex-column untuk mengisi tinggi --}}
            {{-- Saldo --}}
            <div class="card mb-4 flex-grow-0"> {{-- mb-4 untuk jarak ke elemen berikutnya, flex-grow-0 agar tidak membesar --}}
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="text-secondary">Saldo Kamu</strong>
                        <h1 class="mb-0">Rp {{ number_format($balance, 0, ',', '.') }}</h1>
                    </div>
                    <div class="d-flex gap-2">
                        <button
                            class="btn btn-dark d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;"
                            data-bs-toggle="modal"
                            data-bs-target="#depositModal"
                            title="Deposit"
                        >
                            <i class="ti ti-plus fs-4"></i>
                        </button>

                        <button type="button" class="btn btn-dark d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;"
                            data-bs-toggle="modal" data-bs-target="#withdrawModal"
                            title="Withdraw">
                            <i class="ti ti-arrow-up fs-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="card flex-grow-1"> {{-- flex-grow-1 agar card ini memenuhi sisa tinggi yang tersedia --}}
                <div class="card-body">
                    <h4 class="card-title">Statistik Transaksi Minggu Ini</h4>
                    <div id="weekly-chart" style="height: 200px;"></div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan (Histori Transaksi) --}}
        <div class="col-12 col-lg-7"> {{-- col-12 untuk mobile, col-lg-7 untuk layar besar --}}
            <div class="card h-100"> {{-- h-100 agar tinggi card sama dengan kolom kiri --}}
                <div class="card-body">
                    <h3 class="card-title">Histori Transaksi</h3>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Tipe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $trx)
                                    <tr>
                                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                        <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                        <td>
                                            @if($trx->status == 'pending')
                                                <span class="badge bg-warning-lt">Pending</span>
                                            @elseif($trx->status == 'approved')
                                                <span class="badge bg-success-lt">Approved</span>
                                            @else
                                                <span class="badge bg-danger-lt">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($trx->type) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Deposit --}}
<div class="modal modal-blur fade" id="depositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('student.deposit.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="saving_segment_id" value="{{ $segment->id }}">
            <div class="modal-header">
                <h5 class="modal-title">Ajukan Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="amount" class="form-label">Jumlah Deposit (IDR)</label>
                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        class="form-control @error('amount') is-invalid @enderror"
                        min="1000"
                        placeholder="Contoh: 50000"
                        required
                        value="{{ old('amount') }}">
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Ajukan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const options = {
        chart: {
            type: 'line',
            height: 200,
            toolbar: { show: false }
        },
        series: [{
            name: "Transaksi",
            data: {!! json_encode($weeklyStats['data']) !!}
        }],
        xaxis: {
            categories: {!! json_encode($weeklyStats['labels']) !!}
        },
        colors: ['#206bc4'],
        stroke: {
            curve: 'smooth',
            width: 2
        }
    };
    new ApexCharts(document.querySelector("#weekly-chart"), options).render();
});
</script>
@endpush