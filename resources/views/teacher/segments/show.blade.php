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
        {{-- Progress Segment Card (Left Column for Stacked Progress Bar) --}}
<div class="col-md-6 col-lg-4">
    <div class="card text-light" style="background-color: #37A2EA;box-shadow: 10px 10px 0px 0px #1086D5;
-webkit-box-shadow: 10px 10px 0px 0px #1086D5;
-moz-box-shadow: 10px 10px 0px 0px #1086D5;">
        <div class="card-header">
            <h3 class="card-title">Progress Tabungan</h3>
        </div>
        <div class="card-body text-center">
            @if($currentBalance > 0 || $segment->target_amount > 0)
               

                <h3 class="mt-2 fs-2 fw-bold">{{ $segment->description }}
                    <span class="text-success"></span>
                </h3>

                

                {{-- Stacked Progress Bar --}}
                <div class="progress mt-4" style="height: 1rem; position: relative;">
    @php
        $baseColor = '#1086D5'; // satu warna dasar
        $totalTarget = $segment->target_amount > 0 ? $segment->target_amount : 1;
    @endphp

    @foreach ($contributions as $index => $contributor)
        @php
            $percentage = ($contributor['amount'] / $totalTarget) * 100;
            $tooltip = "{$contributor['name']} - Rp " . number_format($contributor['amount'], 0, ',', '.');

            // Buat gradasi saturasi meningkat: mulai dari 1.0 ke atas
            $saturateLevel = 1 + ($index * 1); // bisa atur ke 0.1 untuk lebih halus
        @endphp

        <div class="progress-bar"
             style="width: {{ $percentage }}%; background-color: {{ $baseColor }}; filter: saturate({{ $saturateLevel }});"
             title="{{ $tooltip }}"
             data-bs-toggle="tooltip"
             data-bs-placement="top">
        </div>
    @endforeach
</div>


                <p class="fs-3 fw-semibold mt-3">Rp {{ number_format($currentBalance, 0, ',', '.') }} / Rp {{ number_format($segment->target_amount, 0, ',', '.') }}</p>
                <div class="mt-2 text-muted">
                   
                </div>
            @else
                <div class="py-4 text-muted">
                    Belum ada data tabungan untuk segmen ini.
                    <br>Mulai ajak siswa menabung!
                </div>
            @endif

            <a href="{{ route('teacher.segments.students', $segment) }}" style="background-color: #107BFF;" class="btn btn-info w-100 mt-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                     stroke-linejoin="round">
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