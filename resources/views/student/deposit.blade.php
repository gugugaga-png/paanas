@extends('layouts.app')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Ajukan Tabungan Dana
                </h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($segment)
                <div class="alert alert-info">
                    Anda akan mengajukan tabungan ke segment: <strong>{{ $segment->name }}</strong> (Kode: {{ $segment->unique_code }})
                </div>
            @endif

            <form action="{{ route('student.deposit') }}" method="POST"> {{-- route masih tetap 'student.deposit' --}}
                @csrf
                <div class="mb-3">
                    <label class="form-label">Pilih Segment Tabungan</label>
                    <select name="saving_segment_id" class="form-select @error('saving_segment_id') is-invalid @enderror" required>
                        <option value="">Pilih Segment</option>
                        @foreach ($userSegments as $id => $name)
                            <option value="{{ $id }}" {{ old('saving_segment_id', $segment->id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                        @if ($segment && !$userSegments->has($segment->id))
                            <option value="{{ $segment->id }}" selected>{{ $segment->name }} (Baru ditemukan)</option>
                        @endif
                    </select>
                    @error('saving_segment_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Hanya segmen yang sudah pernah Anda tabung akan muncul di sini secara otomatis. Jika ingin menabung ke segmen baru, gunakan fitur "Gabung Segment".</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Tabungan (Rp)</label>
                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="Contoh: 10000" value="{{ old('amount') }}" required min="1000">
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Ajukan Tabungan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection