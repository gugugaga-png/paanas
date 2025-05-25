@extends('layouts.app') {{-- Make sure this extends your main layout file --}}

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Gabung Segment Tabungan
                </h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Display success/error messages --}}
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
</div>
@endsection