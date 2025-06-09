@extends('layouts.app')

@section('content')
<div class="modal fade" id="joninsegmentModal" tabindex="-1" aria-labelledby="joninsegmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('student.join_segment') }}" method="POST">
                @csrf {{-- CSRF token is crucial for form security --}}

                <div class="mb-3">
                    <label class="form-label" for="unique_code">Kode Unik Segment</label>
                    <input type="text"
                           name="unique_code"
                           id="unique_code"
                           class="form-control @error('unique_code') is-invalid @enderror"
                           placeholder="Masukkan kode unik dari guru"
                           value="{{ old('unique_code') }}"
                           required
                           maxlength="8"
                           minlength="8"
                           pattern="[A-Za-z0-9]{8}" {{-- Optional: enforce alphanumeric 8 chars --}}
                           title="Kode unik harus 8 karakter alfanumerik (huruf dan angka)">
                    @error('unique_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Cari & Gabung</button>
                </div>
            </form>
        </div>
    </div>
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('student.deposit.store') }}" method="POST" id="depositForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="depositModalLabel">Deposit Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="deposit_segment_id" class="form-label">Pilih Segment</label>
                        <select class="form-select" id="deposit_segment_id" name="saving_segment_id" required>
                            <option value="" disabled selected>-- Pilih Segment --</option>
                            @foreach ($joinedSegments as $segment)
                                <option value="{{ $segment->id }}">
                                    {{ $segment->name }} (Saldo: Rp {{ number_format($segment->balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="deposit_amount" class="form-label">Jumlah Deposit (Rp)</label>
                        <input type="number" class="form-control" id="deposit_amount" name="amount" min="1000" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Deposit</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('student.withdraw.store') }}" method="POST" id="withdrawForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">Withdraw Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="segment_id" class="form-label">Pilih Segment</label>
                        <select class="form-select" id="segment_id" name="saving_segment_id" required> {{-- name diubah menjadi saving_segment_id sesuai dengan controller --}}
                            <option value="" disabled selected>-- Pilih Segment --</option>
                            @foreach ($joinedSegments as $segment)
                                <option value="{{ $segment->id }}" data-balance="{{ $segment->balance }}">
                                    {{ $segment->name }} (Saldo: Rp {{ number_format($segment->balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah Withdraw (Rp)</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="1000" required> {{-- min disesuaikan dengan validasi di controller (minimal Rp 1000) --}}
                        <div id="balanceHelp" class="form-text text-muted">Saldo tersedia: Rp 0</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Withdraw</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container-xl">
    {{-- Row Utama dengan 3 Kolom --}}
    <div class="row g-4">

        {{-- Kolom 1 --}}
        <div class="col-lg-4">
            {{-- Saldo Total --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <div class="text-muted d-flex align-items-center gap-2 fs-4 mb-1">
                                <span>Total Saldo</span>
                                <span class="{{ $saldoGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $saldoGrowth >= 0 ? '+' : '' }}{{ number_format($saldoGrowth, 1) }}%
                                </span>
                            </div>
                            <div class="fw-bold fs-1">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="d-flex gap-2">
    <button type="button" class="btn btn-dark btn-pill" data-bs-toggle="modal" data-bs-target="#depositModal"> {{-- Diubah: Menjadi tombol untuk membuka modal --}}
        Deposit
    </button>
    <button type="button" class="btn btn-outline-dark btn-pill" data-bs-toggle="modal" data-bs-target="#withdrawModal">
        Withdraw
    </button>
</div>
                    
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Segment --}}
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fs-3">Daftar Segment</div>
                    <a href="{{ route('student.join.segment.form') }}" class="btn btn-dark btn-pill"><i class="ti ti-plus"></i></a> {{-- Diubah: Tambahkan link ke rute join segment --}}
                </div>

                <ul class="list-group list-group-flush">
                    @forelse ($joinedSegments as $segment)
                        <li class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center">
                            <a href="{{ route('student.segment.detail', $segment->id) }}"
                               class="d-flex align-items-center text-decoration-none text-body flex-grow-1 gap-3">
                                <img src="{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}"
                                     alt="Banner" class="avatar avatar-md rounded-circle" style="object-fit: cover;">
                                <div>
                                    <div class="fw-bold fs-5 mb-1">{{ $segment->name }}</div>
                                    <div class="text-primary small">{{ $segment->unique_code }}</div>
                                </div>
                            </a>
                            <div class="text-end fw-bold" style="min-width: 130px;">
                                Rp {{ number_format($segment->balance, 0, ',', '.') }}
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">Belum bergabung dengan segment tabungan manapun.</li>
                    @endforelse
                </ul>
            </div>

        </div>

        {{-- Kolom 2 --}}
        <div class="col-lg-4">
            {{-- Chart Pemasukan vs Pengeluaran --}}
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">Pemasukan vs Pengeluaran Mingguan</h3>
                </div>
                <div class="card-body">
                    <canvas id="incomeExpenseBarChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Kolom 3 --}}
        <div class="col-lg-4">
            {{-- Tabel Transaksi --}}
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Riwayat Transaksi Tabungan</h3>
                    <select id="filterStatus" class="form-select w-auto">
                        <option value="all">Semua</option>
                        <option value="pending">Menunggu Validasi</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter text-nowrap datatable" id="transactionTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Segment Tabungan</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingTransactions as $transaction)
                                <tr data-status="pending">
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $transaction->savingSegment->name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-warning-lt">Menunggu Validasi</span></td>
                                    <td>-</td>
                                </tr>
                            @endforeach
                            @foreach ($approvedTransactions as $transaction)
                                <tr data-status="approved">
                                    <td>{{ $transaction->updated_at->format('d M Y H:i') }}</td>
                                    <td>{{ $transaction->savingSegment->name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-success-lt">Disetujui</span></td>
                                    <td>{{ $transaction->approver->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                            @foreach ($rejectedTransactions as $transaction)
                                <tr data-status="rejected">
                                    <td>{{ $transaction->updated_at->format('d M Y H:i') }}</td>
                                    <td>{{ $transaction->savingSegment->name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-danger-lt">Ditolak</span></td>
                                    <td>{{ $transaction->approver->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Chart Pendapatan Mingguan --}}
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Pendapatan Mingguan: Bulan Ini vs Bulan Lalu</h3>
        </div>
        <div class="card-body">
            <canvas id="weeklyIncomeBarChart" height="100"></canvas> {{-- ID Disesuaikan --}}
        </div>
    </div>

    {{-- Segment Pilihan --}}
    <div class="mb-4 mt-4">
        <label class="form-label d-block">Pilih Segment Tabungan</label>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            @foreach ($joinedSegments as $segment)
                <div class="col">
                    <a href="{{ route('student.segment.detail', $segment->id) }}" class="card h-100 text-decoration-none text-body">
                        <div class="position-relative overflow-hidden" style="padding-top: 35%;">
                            <img src="{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}"
                                 alt="Banner"
                                 class="card-img-top"
                                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
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

    {{-- Alerts --}}
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

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- Chart Bar untuk Pemasukan vs Pengeluaran (Mingguan) ---
    const incomeExpenseBarCtx = document.getElementById('incomeExpenseBarChart').getContext('2d');

    const incomeExpenseBarChart = new Chart(incomeExpenseBarCtx, {
        type: 'bar', // Ubah ke 'bar'
        data: {
            labels: {!! json_encode($weeklyLabels) !!},
            datasets: [
                {
                    label: 'Pemasukan',
                    data: {!! json_encode($weeklyIncome) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.8)', // Warna batang untuk pemasukan
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    borderRadius: 5 // Sudut membulat
                },
                {
                    label: 'Pengeluaran',
                    data: {!! json_encode($weeklyExpense) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.8)', // Warna batang untuk pengeluaran
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    borderRadius: 5 // Sudut membulat
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: { // Tambahkan konfigurasi sumbu x untuk bar chart
                    grid: {
                        display: false // Sembunyikan garis grid vertikal jika tidak dibutuhkan
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // --- Chart Bar untuk Pendapatan Mingguan: Bulan Ini vs Bulan Lalu ---
    // Pastikan Anda sudah mengisi $weeklyIncomeThisMonth dan $weeklyIncomeLastMonth di controller
    const weeklyIncomeBarChartCtx = document.getElementById('weeklyIncomeBarChart').getContext('2d'); // Perbaiki ID di sini
    new Chart(weeklyIncomeBarChartCtx, {
        type: 'bar', // Ubah ke 'bar'
        data: {
            // Pastikan weeklyLabels di sini juga disesuaikan untuk 4 minggu,
            // atau Anda bisa membuat variabel baru di controller (misal: $monthlyWeeklyLabels)
            labels: {!! json_encode($monthlyWeeklyLabels) !!}, // Gunakan $monthlyWeeklyLabels untuk chart kedua
            datasets: [
                {
                    label: 'Bulan Ini',
                    data: {!! json_encode($weeklyIncomeThisMonth ?? []) !!}, // Pastikan ini ada dan tidak kosong
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Bulan Lalu',
                    data: {!! json_encode($weeklyIncomeLastMonth ?? []) !!}, // Pastikan ini ada dan tidak kosong
                    backgroundColor: 'rgba(255, 206, 86, 0.8)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: { // Tambahkan konfigurasi sumbu x untuk bar chart
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const segmentSelect = document.getElementById('segment_id');
        const amountInput = document.getElementById('amount');
        const balanceHelp = document.getElementById('balanceHelp');

        

        function updateBalance() {
            const selectedOption = segmentSelect.options[segmentSelect.selectedIndex];
            // Pastikan untuk mengambil data-balance hanya jika selectedOption bukan disabled/placeholder
            const balance = selectedOption && selectedOption.value !== "" ? parseInt(selectedOption.dataset.balance) : 0;
            balanceHelp.textContent = `Saldo tersedia: Rp ${balance.toLocaleString('id-ID')}`;

            // Reset amount input when segment changes
            amountInput.value = '';
            amountInput.max = balance; // Set max attribute for amount input
        }

        segmentSelect.addEventListener('change', updateBalance);

        // Validasi input amount tidak boleh lebih dari saldo
        amountInput.addEventListener('input', function() {
            const max = parseInt(this.max);
            const val = parseInt(this.value);
            if (val > max) {
                this.value = max;
            } else if (val < parseInt(this.min)) { // Pastikan tidak kurang dari min
                this.value = parseInt(this.min);
            }
        });

        // Inisialisasi saat modal dibuka
        var depositModal = document.getElementById('depositModal');
    depositModal.addEventListener('show.bs.modal', function () {
        // Reset select to default option
        document.getElementById('deposit_segment_id').value = "";
        // Reset amount input
        document.getElementById('deposit_amount').value = "";
    });
        var withdrawModal = document.getElementById('withdrawModal');
        withdrawModal.addEventListener('show.bs.modal', function () {
            // Reset select to default option
            segmentSelect.value = "";
            updateBalance(); // Update balance to 0 and reset amount input
        });

        // Pastikan updateBalance dipanggil saat halaman pertama kali dimuat jika ada segment yang sudah terpilih secara default
        if (segmentSelect.value !== "") {
            updateBalance();
        }
    });
    

    // JavaScript untuk filter tabel transaksi tetap sama
    document.getElementById('filterStatus').addEventListener('change', function () {
        const status = this.value;
        const rows = document.querySelectorAll('#transactionTable tbody tr');

        rows.forEach(row => {
            const rowStatus = row.dataset.status;
            if (status === 'all' || status === rowStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
</script>
@endpush