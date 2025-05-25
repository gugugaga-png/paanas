@extends('layouts.app') {{-- Assuming you have a layout --}}

@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <form action="{{ route('teacher.segments.store') }}" method="POST" class="card">
                    @csrf
                    <div class="card-header">
                        <h3 class="card-title">Buat Segment Tabungan Baru</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Segment</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Tabungan Liburan" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Deskripsi singkat tentang tujuan tabungan ini">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Jumlah (IDR)</label>
                            <input type="number" name="target_amount" class="form-control @error('target_amount') is-invalid @enderror" placeholder="Contoh: 1000000" value="{{ old('target_amount') }}" min="0" required>
                            @error('target_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Unik Segment</label>
                            <input type="text" name="unique_code" class="form-control @error('unique_code') is-invalid @enderror" placeholder="Otomatis terisi atau buat sendiri" value="{{ old('unique_code', $uniqueCode ?? '') }}" required>
                            @error('unique_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Buat Segment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection