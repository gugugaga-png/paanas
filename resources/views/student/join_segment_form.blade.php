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

            
        </div>
    </div>
</div>
@endsection