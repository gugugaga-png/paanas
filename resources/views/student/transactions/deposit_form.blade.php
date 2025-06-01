@extends('layouts.app') {{-- Extend your main layout --}}

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Ajukan Deposit Tabungan
                </h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
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
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('student.submit_deposit') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="saving_segment_id">Pilih Segment Tabungan</label>
                    <select name="saving_segment_id" id="saving_segment_id" class="form-select @error('saving_segment_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Segment --</option>
                        @foreach ($segments as $segment)
                            <option value="{{ $segment->id }}" {{ old('saving_segment_id') == $segment->id ? 'selected' : '' }}>
                                {{ $segment->name }} (Kode: {{ $segment->unique_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('saving_segment_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="amount">Jumlah Deposit (IDR)</label>
                    <input type="number"
                           name="amount"
                           id="amount"
                           class="form-control @error('amount') is-invalid @enderror"
                           placeholder="Contoh: 50000"
                           value="{{ old('amount') }}"
                           required
                           min="1000"> {{-- Minimum deposit amount --}}
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Ajukan Deposit</button>
                </div>
            </form>
           

        </div>
    </div>
</div>
@endsection