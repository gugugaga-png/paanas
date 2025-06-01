{{-- resources/views/teacher/segments/show_students.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Daftar Siswa di Segmen: {{ $segment->name }} <span class="text-muted small">({{ $segment->unique_code }})</span>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('teacher.segments.show', $segment) }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" /></svg>
                        Kembali ke Detail Segmen
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Daftar Siswa & Saldo Tabungan</h3>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Saldo Segment (Rp)</th> {{-- Ubah header agar lebih spesifik --}}
                    </tr>
                </thead>
                <tbody>
    {{-- Ganti $students menjadi $studentsWithBalances --}}
    @forelse ($studentsWithBalances as $studentBalance)
        <tr>
            <td>{{ $studentBalance->user->name ?? 'N/A' }}</td>
            {{-- Add this line to display the balance --}}
            <td>{{ number_format($studentBalance->balance ?? 0, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="2" class="text-center">Tidak ada siswa dengan saldo di segmen ini.</td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>
</div>
@endsection