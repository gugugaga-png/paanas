@extends('layouts.app')

@section('content')
<div class="modal fade" id="joninsegmentModal" tabindex="-1" aria-labelledby="joninsegmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('student.join_segment') }}" method="POST">
            @csrf {{-- CSRF token is crucial for form security --}}

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="joninsegmentModalLabel">Gabung Segment Tabungan</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unique_code" class="form-label">Kode Unik Segment</label>
                        <input
                            type="text"
                            name="unique_code"
                            id="unique_code"
                            class="form-control @error('unique_code') is-invalid @enderror"
                            placeholder="Masukkan kode unik dari guru"
                            value="{{ old('unique_code') }}"
                            required
                            maxlength="8"
                            minlength="8"
                            pattern="[A-Za-z0-9]{8}"
                            title="Kode unik harus 8 karakter alfanumerik (huruf dan angka)"
                        >
                        @error('unique_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Cari & Gabung</button>
                </div>
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
    <div class="row g-4 align-items-stretch"> {{-- Tetap gunakan align-items-stretch pada baris utama --}}

        {{-- KOLOM KIRI: Saldo dan Segment (col-lg-4) --}}
        <div class=" d-flex flex-column"> {{-- Tambahkan d-flex flex-column di sini --}}
            {{-- Saldo Total --}}
            <div class="row row-deck mb-4">
    {{-- Total Saldo --}}
    <div class="col-12 col-sm-6 col-md-3 col">
        <div class="card">
            <div class="card-body">
                <div class="text-secondary mb-1">Total Saldo</div>
                <div class="h2 fw-bold">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
                <div class="d-flex gap-2">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-dark btn-pill" data-bs-toggle="modal" data-bs-target="#depositModal">
                                    Deposit
                                </button>
                                <button type="button" class="btn btn-outline-dark btn-pill " data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                    Withdraw
                                </button>
                            </div>
                        </div>
            </div>
        </div>
    </div>

    {{-- Total Segment --}}
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-secondary mb-1">Total Segment</div>
                <div class="h2 fw-bold">{{ $totalJoinedSegmentsCount }} Segment</div>
            </div>
        </div>
    </div>

    {{-- Pengeluaran Bulan Ini --}}
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-secondary mb-1">Pengeluaran Bulan Ini</div>
                <div class="h2 fw-bold text-danger">Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Pendapatan Bulan Ini --}}
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-secondary mb-1">Pendapatan Bulan Ini</div>
                <div class="h2 fw-bold text-success">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

            {{-- Daftar Segment --}}

            <div class="row ">
    @forelse ($joinedSegments as $segment)
        <div class="col-12 col-md-3 col-xl-5th">
            <a href="{{ route('student.segment.detail', $segment->id) }}" class="text-decoration-none text-dark">
                <div class="card shadow-sm border-0 overflow-hidden rounded-3 h-100" style="position: relative;">
                    {{-- Gambar banner --}}
                    <div class="position-relative" style="aspect-ratio: 3 / 1;">
                        <div class="w-100 h-100"
                            style="background-image: url('{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}');
                                background-size: cover;
                                background-position: center;">
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(0, 0, 0, 0.12);"></div>
                        <div class="position-absolute top-0 start-0 text-white px-3 py-3 w-100">
                            <h4 class="fw-normal fs-2 mb-0">{{ $segment->name }}</h4>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="px-3 py-3 border-top" style="height: 145px;">
                        <div class="fw-normal    fs-4 text-dark">{{ $segment->description }}</div>
                 </div>

                    {{-- Footer --}}
                    <div class="card-footer ">
                       </i><div class="fw-bold text-dark"> <i class="ti ti-wallet p-1 rounded bg-blue-lt"></i> Rp {{ number_format($segment->balance, 0, ',', '.') }}</div>
                    </div>

                    <span class="stretched-link"></span>
                </div>
            </a>
        </div>
    @empty
        <p class="text-center">Tidak ada segment yang diikuti.</p>
    @endforelse
</div>



            
        </div>

        {{-- KOLOM KANAN: Chart dan Riwayat Transaksi (col-lg-8) --}}
        
    </div>
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