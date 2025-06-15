@extends('layouts.app')

@section('content')

{{-- Modal Ajukan Penarikan --}}
<div class="modal modal-blur fade" id="withdrawModal" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('student.withdraw.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="saving_segment_id" value="{{ $segment->id }}">
            <div class="modal-header">
                <h5 class="modal-title" id="withdrawModalLabel">Ajukan Penarikan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="withdraw_amount" class="form-label">Jumlah Penarikan (IDR)</label>
                    <input
                        type="number"
                        name="amount"
                        id="withdraw_amount"
                        class="form-control @error('amount', 'withdraw') is-invalid @enderror" {{-- Menambahkan scope error --}}
                        min="1000"
                        placeholder="Contoh: 25000"
                        required
                        value="{{ old('amount') }}"
                    >
                    @error('amount', 'withdraw') {{-- Menambahkan scope error --}}
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
    {{-- Banner Informasi Segmen --}}
    <div
        class="card card-cover card-status-top-orange mb-4 rounded-3"
        style="background-image: url('{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}'); background-size: cover; background-position: center; min-height: 250px; position: relative;"
        role="img"
        aria-label="Banner untuk segmen {{ $segment->name }}"
    >
        {{-- Overlay untuk teks agar lebih terbaca --}}
        <div style="
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.4) 100%); {{-- Memberikan sedikit gradient gelap di bawah --}}
            border-radius: inherit;
        "></div>

        <div
            class="card-body d-flex flex-column justify-content-end align-items-start text-white p-4" {{-- Menambahkan padding --}}
            style="position: absolute; bottom: 0; left: 0; right: 0; z-index: 2;"
        >
            <h1 class="display-4 fw-bold mb-2">{{ $segment->name }}</h1>
            <p class="mb-0">{{ $segment->description ?? 'Tidak ada deskripsi' }}</p> {{-- Menghilangkan margin-bottom jika tidak ada elemen setelahnya --}}
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Kolom Kiri: Saldo dan Statistik --}}
        <div class="col-12 col-lg-5 d-flex flex-column">
            {{-- Card Saldo --}}
            <div class="card mb-4 flex-grow-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="text-secondary">Saldo Kamu</strong>
                        <h2 class="mb-0">Rp {{ number_format($balance, 0, ',', '.') }}</h2> {{-- Mengubah h1 menjadi h2 untuk hierarki yang lebih baik --}}
                    </div>
                    <div class="d-flex gap-2">
                        <button
                            class="btn btn-dark d-flex align-items-center justify-content-center btn-icon" {{-- Menambahkan btn-icon untuk ukuran tetap --}}
                            data-bs-toggle="modal"
                            data-bs-target="#depositModal"
                            title="Ajukan Deposit"
                            aria-label="Ajukan Deposit"
                        >
                            <i class="ti ti-plus fs-4"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-dark d-flex align-items-center justify-content-center btn-icon" {{-- Menambahkan btn-icon untuk ukuran tetap --}}
                            data-bs-toggle="modal"
                            data-bs-target="#withdrawModal"
                            title="Ajukan Penarikan"
                            aria-label="Ajukan Penarikan"
                        >
                            <i class="ti ti-arrow-up fs-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Card Statistik Mingguan dengan Pemasukan vs Pengeluaran (Garis) --}}
            <div class="card flex-grow-1"> {{-- Biarkan ini flex-grow-1 agar mengisi sisa ruang --}}
                <div class="card-body">
                    <h4 class="card-title">Statistik Transaksi Mingguan (Deposit vs Penarikan)</h4>
                    <div id="weekly-chart" style="min-height: 200px;"></div>
                    {{-- Ringkasan Total Deposit dan Penarikan --}}
                    <div class="d-flex justify-content-around mt-3">
                        <div class="text-center">
                            <div class="text-success fw-bold fs-3">Rp {{ number_format($totalDeposits ?? 0, 0, ',', '.') }}</div>
                            <div class="text-muted">Total Deposit</div>
                        </div>
                        <div class="text-center">
                            <div class="text-danger fw-bold fs-3">Rp {{ number_format($totalWithdrawals ?? 0, 0, ',', '.') }}</div>
                            <div class="text-muted">Total Penarikan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Histori Transaksi --}}
        <div class="col-12 col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title">Histori Transaksi</h3>

                    @if ($transactions->isEmpty())
                        <div class="alert alert-info text-center" role="alert">
                            Belum ada transaksi di segmen ini.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-vcenter"> {{-- Menambahkan table-vcenter untuk perataan vertikal --}}
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Tipe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $trx)
                                        <tr>
                                            <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                            <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                            <td>
                                                @if($trx->status == 'pending')
                                                    <span class="badge bg-warning-lt">Pending</span>
                                                @elseif($trx->status == 'approved')
                                                    <span class="badge bg-success-lt">Disetujui</span>
                                                @else
                                                    <span class="badge bg-danger-lt">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($trx->type) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Ajukan Deposit --}}
<div class="modal modal-blur fade" id="depositModal" tabindex="-1" role="dialog" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('student.deposit.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="saving_segment_id" value="{{ $segment->id }}">
            <div class="modal-header">
                <h5 class="modal-title" id="depositModalLabel">Ajukan Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="deposit_amount" class="form-label">Jumlah Deposit (IDR)</label>
                    <input
                        type="number"
                        name="amount"
                        id="deposit_amount"
                        class="form-control @error('amount', 'deposit') is-invalid @enderror" {{-- Menambahkan scope error --}}
                        min="1000"
                        placeholder="Contoh: 50000"
                        required
                        value="{{ old('amount') }}"
                    >
                    @error('amount', 'deposit') {{-- Menambahkan scope error --}}
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
        // --- Bagan Statistik Transaksi Mingguan (Line Chart dengan Deposit vs Penarikan) ---
        const weeklyChartElement = document.querySelector("#weekly-chart");
        if (weeklyChartElement) {
            // Pastikan data deposits dan withdrawals ada dari controller
            const weeklyStatsDeposits = {!! json_encode($weeklyStats['deposits'] ?? []) !!};
            const weeklyStatsWithdrawals = {!! json_encode($weeklyStats['withdrawals'] ?? []) !!};

            const weeklyOptions = {
                chart: {
                    type: 'line',
                    height: 200,
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                series: [{
                    name: "Setoran",
                    data: weeklyStatsDeposits
                }, {
                    name: "Penarikan",
                    data: weeklyStatsWithdrawals
                }],
                xaxis: {
                    categories: {!! json_encode($weeklyStats['labels'] ?? []) !!},
                    labels: {
                        show: true,
                        rotate: -45,
                        trim: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                // Warna garis
                colors: ['#55B2DB', '#DF775E'],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                // Efek gradasi di bawah garis
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7, // Opasitas awal
                        opacityTo: 0,     // Memudar hingga transparan
                        stops: [0, 100],
                        // Mendefinisikan colorStops untuk setiap seri
                        colorStops: [
                            [ // Color stops untuk Seri Setoran (Deposit)
                                {
                                    offset: 0,
                                    color: '#55B2DB',
                                    opacity: 0.7
                                },
                                {
                                    offset: 100,
                                    color: '#55B2DB',
                                    opacity: 1
                                }
                            ],
                            [ // Color stops untuk Seri Penarikan (Withdrawal)
                                {
                                    offset: 0,
                                    color: '#DF775E',
                                    opacity: 0.7
                                },
                                {
                                    offset: 100,
                                    color: '#DF775E',
                                    opacity: 1
                                }
                            ]
                        ]
                    }
                },
                grid: {
                    borderColor: '#e0e6ed',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    show: true, // Tampilkan legend karena ada lebih dari satu seri
                    position: 'bottom'
                }
            };
            new ApexCharts(weeklyChartElement, weeklyOptions).render();
        }

        // Menampilkan modal withdraw jika ada error validasi
        @if ($errors->withdraw->any())
            var withdrawModal = new bootstrap.Modal(document.getElementById('withdrawModal'));
            withdrawModal.show();
        @endif

        // Menampilkan modal deposit jika ada error validasi
        @if ($errors->deposit->any())
            var depositModal = new bootstrap.Modal(document.getElementById('depositModal'));
            depositModal.show();
        @endif
    });
</script>
@endpush
