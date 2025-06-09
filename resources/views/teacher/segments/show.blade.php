@extends('layouts.app')

@section('content')
<div class="container-xl">
    {{-- Full Width Banner for Segment Info --}}
    <div class="card card-cover card-status-top-orange mb-4"
        style="
            background-image: url('{{ $segment->banner ? asset('storage/' . $segment->banner) : asset('images/default.png') }}'); 
            background-size: cover; 
            background-position: center; 
            min-height: 250px; 
            position: relative;
            border-radius: 0.5rem;
        ">

        {{-- Overlay gradient --}}
        <div style="
            position: absolute; 
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.5) 100%);
            border-radius: inherit;
        "></div>

        {{-- Content inside banner --}}
        <div class="card-body d-flex flex-column justify-content-end align-items-start text-white"
            style="
                position: absolute; 
                bottom: 20px; 
                left: 20px; 
                right: 20px; 
                z-index: 2;
                text-align: left;
            ">

            <h1 class="display-5 fw-bold mb-2">{{ $segment->name }}</h1>
            <p class="mb-2">{{ $segment->description ?? 'Tidak ada deskripsi' }}</p>
            <p class="mb-1">Dibuat oleh: {{ $segment->user->name ?? 'Tidak diketahui' }}</p>
            <span class="badge bg-green-lt">Kode Unik: {{ $segment->unique_code }}</span>

        </div>
    </div>

    <div class="row row-cards">
        {{-- Progress Segment Card (Left Column for Chart) --}}
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Progress Tabungan</h3>
                </div>
                <div class="card-body text-center">
                    @if($currentBalance > 0 || $segment->target_amount > 0) {{-- Render chart if there's any balance or a target --}}
                        <p class="mb-1 text-muted" style="font-size: 0.9rem;">Target: Rp {{ number_format($segment->target_amount, 0, ',', '.') }}</p>
                        <div class="position-relative d-inline-block" style="width: 300px; height: 300px;">
                            <div id="segment-balance-chart" style="width: 100%; height: 100%;"></div>

                            {{-- Persentase progress di depan chart --}}
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <div class="fw-bold fs-3 text-success">
                                    {{ round(($currentBalance / ($segment->target_amount > 0 ? $segment->target_amount : 1)) * 100, 1) }}%
                                </div>
                                <small class="text-muted">Tercapai</small>
                            </div>
                        </div>
                        {{-- Summary Below Chart --}}
                        <h3 class="mt-3">Total Terkumpul: <span class="text-success">Rp {{ number_format($currentBalance, 0, ',', '.') }}</span></h3>
                        <p class="text-secondary">Sisa Target: Rp {{ number_format(max(0, $segment->target_amount - $currentBalance), 0, ',', '.') }}</p>
                    @else
                        <div class="py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-pie-off text-muted" width="80" height="80" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                               <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                               <path d="M10.59 6.582l1.41 -.582l.006 -2.484" />
                               <path d="M12 3v9h9" />
                               <path d="M18.895 14.897c-.006 .006 -.01 .012 -.016 .018m-2.923 1.053l-3.956 3.956c-.537 .538 -1.334 .748 -2.096 .586l-2.731 -.673a1.921 1.921 0 0 0 -1.411 -.183c-.767 .202 -1.48 .704 -1.82 1.343l-1.393 2.658h-.002a.009 .009 0 0 1 -.005 -.005a1 1 0 0 0 -1 1c0 .563 .378 1.037 .899 1.282l2.646 1.267c.725 .347 1.488 .298 2.155 -.163l4.633 -3.279m.062 -4.397a9 9 0 0 0 -8.995 -8.994m-2.983 .99c-.067 .342 -.11 .699 -.117 1.066m10.117 10.134a9 9 0 0 0 2.87 -1.09" />
                               <path d="M3 3l18 18" />
                            </svg>
                            <p class="text-muted mt-3">
                                Belum ada data tabungan untuk segmen ini.
                                <br>Mulai ajak siswa menabung!
                            </p>
                        </div>
                    @endif

                    {{-- Tombol untuk melihat Data Siswa (di luar kondisi chart) --}}
                    <a href="{{ route('teacher.segments.students', $segment) }}" class="btn btn-info w-100 mt-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        Lihat Data Siswa
                    </a>
                </div>
            </div>
        </div>

        {{-- Tabel Riwayat Transaksi (Right Column) --}}
        <div class="col-md-6 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Transaksi Segment Ini</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
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

                {{-- PAGINATION --}}
                @if ($transactions->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $transactions->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // currentBalance di sini sudah saldo bersih dari controller (setoran - penarikan)
        const currentBalance = {{ $currentBalance }}; 
        const target = {{ $segment->target_amount }};
        const remaining = Math.max(target - currentBalance, 0);

        const chartContainer = document.querySelector("#segment-balance-chart");
        // Kondisi ini sudah cukup untuk tidak merender chart jika tidak ada data sama sekali
        if (!chartContainer || (currentBalance === 0 && target === 0)) {
            return; 
        }

        const contributions = @json($contributions); // Ini sudah berisi saldo bersih per siswa

        // Kita hanya ingin menampilkan kontribusi positif siswa di chart
        // Data 'contributions' dari controller sudah dihitung sebagai saldo bersih (deposit - withdrawal) per siswa
        // dan hanya mengandung siswa dengan netContribution > 0.
        let seriesData = contributions.map(c => c.amount);
        let labelsData = contributions.map(c => c.name);
        
        // Tambahkan 'Sisa Target' jika ada dan target > 0
        if (remaining > 0 && target > 0) {
            seriesData.push(remaining);
            labelsData.push("Sisa Target");
        } else if (currentBalance >= target && target > 0) {
            // Jika saldo sudah mencapai atau melebihi target, tidak perlu "Sisa Target"
            // Tapi jika target_amount adalah 0 dan ada currentBalance, ini akan memicu chart penuh.
            // Biarkan saja, karena `remaining` akan 0
        }


        const tablerColors = [
            tabler.getColor("green"),       
            tabler.getColor("blue"),        
            tabler.getColor("yellow"),      
            tabler.getColor("cyan"),        
            tabler.getColor("purple"),      
            tabler.getColor("orange"),      
            tabler.getColor("pink"),        
            tabler.getColor("red"),         
            tabler.getColor("gray-300"),    
        ];

        // Alokasikan warna untuk setiap kontribusi siswa dan sisa target
        let assignedColors = contributions.map((_, index) => tablerColors[index % (tablerColors.length - 1)]); 
        if (remaining > 0 && target > 0) {
            assignedColors.push(tabler.getColor("red")); // Warna khusus untuk "Sisa Target"
        }
        
        // Hitung persentase terkumpul (sudah benar)
        const percentageAchieved = (target > 0) ? ((currentBalance / target) * 100).toFixed(1) : (currentBalance > 0 ? 100 : 0).toFixed(1);

        new ApexCharts(chartContainer, { 
            chart: {
                type: "donut",
                height: 250, 
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            series: seriesData, // Ini sudah berisi data saldo bersih dari masing-masing kontributor
            labels: labelsData,
            colors: assignedColors,
            legend: {
                position: "bottom", 
                fontSize: '13px',
                fontFamily: 'inherit',
                fontWeight: 500,
                labels: {
                    colors: tabler.getColor("body-color"), 
                },
                markers: {
                    width: 10,
                    height: 10,
                    radius: 12,
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                },
                offsetY: 5 
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "Rp " + val.toLocaleString("id-ID");
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%', 
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '16px',
                                fontFamily: 'inherit',
                                color: tabler.getColor("body-color"),
                                offsetY: -10
                            },
                            value: {
                                show: true,
                                fontSize: '24px',
                                fontFamily: 'inherit',
                                fontWeight: 'bold',
                                color: tabler.getColor("heading-color"),
                                offsetY: 5,
                                formatter: function (val) {
                                    return "Rp " + parseFloat(val).toLocaleString("id-ID");
                                }
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: 'Tercapai', // Label menjadi 'Tercapai'
                                fontSize: '14px',
                                fontFamily: 'inherit',
                                fontWeight: 500,
                                color: tabler.getColor("secondary"),
                                formatter: function() {
                                    return percentageAchieved + '%'; // Menampilkan persentase
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false 
            },
            fill: {
                opacity: 0.9 
            }
        }).render();
    });
</script>
@endpush