@extends('layouts.app')

@section('content')
{{-- Modals --}}
<div class="modal fade" id="joninsegmentModal" tabindex="-1" aria-labelledby="joninsegmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('student.join_segment') }}" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="joninsegmentModalLabel">Gabung Segment Tabungan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
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
                        <select class="form-select" id="segment_id" name="saving_segment_id" required>
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
                        <input type="number" class="form-control" id="amount" name="amount" min="1000" required>
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
{{-- End Modals --}}

<div class="container-xl">
    {{-- Banner Utama --}}
    <div class="position-relative mb-5" style="z-index: 1;">
        <div class="py-5 rounded-3 w-100"
            style="background-image: url('{{ asset('images/mainbanner.png') }}');
                   background-size: cover;
                   background-position: center;
                   min-height: 200px; /* Tinggi minimum banner */
                   display: flex;
                   flex-direction: column;
                   justify-content: flex-end; /* Posisikan konten di bagian bawah banner */
                   align-items: flex-start;
                   padding-left: 20px;
                   padding-bottom: 70px; /* Beri ruang untuk card saldo yang tumpang tindih */">
            <div class="position-absolute top-0 start-0 w-100 h-100 rounded-3" style="background-color: rgba(0, 0, 0, 0.4);"></div>
        </div>

        {{-- Card Saldo Total (ditempatkan sebagian di dalam banner) --}}
        <div class="card card-stacked shadow-sm border-0 rounded-3 bg-dark text-white p-4
                    mx-auto mx-md-auto mx-lg-0" {{-- Tambahan class untuk responsivitas --}}
             style="position: absolute; bottom: -60px; width: 90%; max-width: 400px; z-index: 10;
                    left: 20px; /* Default untuk large screen ke kiri */
                    right: auto; /* Pastikan tidak ada konflik dengan right */
                    transform: none; /* Hilangkan transform default */
                    ">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-75 mb-1">Total Saldo Anda</div>
                    <div class="h2 fw-bold mb-0">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-pill" data-bs-toggle="modal" data-bs-target="#depositModal">
                        Deposit
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-pill text-white" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        Withdraw
                    </button>
                </div>
            </div>
        </div>

    </div>
    <div style="margin-top: 80px;"> {{-- Jarak agar konten di bawah tidak tertutup card saldo --}}
        {{-- Daftar Segment --}}
        <h2 class="mb-3">Segment Tabungan Anda</h2>
        <div class="row g-3">
            @forelse ($joinedSegments as $segment)
                <div class="col-12 col-lg-3 col-xl-5th"> {{-- col-12 untuk tampilan sm & md --}}
                    <a href="{{ route('student.segment.detail', $segment->id) }}" class="text-decoration-none text-dark d-flex h-100">
                        <div class="card shadow-sm my-3 overflow-hidden rounded-3 flex-fill" style="position: relative;">
                            {{-- Gambar banner --}}
                            <div class="position-relative" style="aspect-ratio: 3 / 1;">
                                <div class="w-100 h-100"
                                    style="background-image: url('{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}');
                                           background-size: cover;
                                           background-position: center;">
                                </div>
                                <div class="position-absolute top-0 start-0 w-100 h-100 rounded-3" style="background-color: rgba(0, 0, 0, 0.12);"></div>
                                <div class="position-absolute top-0 start-0 text-white px-3 py-3 w-100">
                                    <h4 class="fw-normal fs-2 mb-0">{{ $segment->name }}</h4>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="px-3 py-3 border-top" style="height: 145px;">
                                <div class="fw-normal fs-4 text-dark">{{ $segment->description }}</div>
                            </div>

                            {{-- Footer --}}
                            <div class="card-footer ">
                                <div class="fw-bold text-dark"> <i class="ti ti-wallet p-1 rounded bg-blue-lt"></i> Rp {{ number_format($segment->balance, 0, ',', '.') }}</div>
                            </div>

                            <span class="stretched-link"></span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center">Tidak ada segment yang diikuti.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const segmentSelect = document.getElementById('segment_id');
        const amountInput = document.getElementById('amount');
        const balanceHelp = document.getElementById('balanceHelp');

        function updateBalance() {
            const selectedOption = segmentSelect.options[segmentSelect.selectedIndex];
            const balance = selectedOption && selectedOption.value !== "" ? parseInt(selectedOption.dataset.balance) : 0;
            balanceHelp.textContent = `Saldo tersedia: Rp ${balance.toLocaleString('id-ID')}`;
            amountInput.value = '';
            amountInput.max = balance;
        }

        if (segmentSelect) {
            segmentSelect.addEventListener('change', updateBalance);
        }

        if (amountInput) {
            amountInput.addEventListener('input', function() {
                const max = parseInt(this.max);
                const val = parseInt(this.value);
                if (val > max) {
                    this.value = max;
                } else if (val < parseInt(this.min)) {
                    this.value = parseInt(this.min);
                }
            });
        }

        var depositModalElement = document.getElementById('depositModal');
        if (depositModalElement) {
            depositModalElement.addEventListener('show.bs.modal', function () {
                const depositSegmentSelect = document.getElementById('deposit_segment_id');
                if (depositSegmentSelect) {
                    depositSegmentSelect.value = "";
                }
                const depositAmountInput = document.getElementById('deposit_amount');
                if (depositAmountInput) {
                    depositAmountInput.value = "";
                }
            });
        }

        var withdrawModalElement = document.getElementById('withdrawModal');
        if (withdrawModalElement) {
            withdrawModalElement.addEventListener('show.bs.modal', function () {
                if (segmentSelect) {
                    segmentSelect.value = "";
                    updateBalance();
                }
            });
        }

        if (segmentSelect && segmentSelect.value !== "") {
            updateBalance();
        }
    });

    const filterStatusElement = document.getElementById('filterStatus');
    if (filterStatusElement) {
        filterStatusElement.addEventListener('change', function () {
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
    }
</script>
@endpush
