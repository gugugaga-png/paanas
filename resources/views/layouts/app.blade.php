<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Menggunakan versi spesifik untuk stabilitas. Anda bisa mengganti '1.0.0-beta20' dengan versi terbaru --}}
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet"/>
    
    {{-- PENTING: Ini adalah link untuk ikon Tabler (ti ti-plus, ti ti-arrow-left, dll.) --}}
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet" />

    @stack('styles')
</head>
{{-- Menggunakan class 'layout-fluid' untuk layout lebar penuh, atau 'layout-condensed' untuk lebih ringkas --}}
<body class="layout-fluid">
    <div class="page">
        {{-- Sidebar (jika tidak di halaman otentikasi) --}}
        @if (!in_array(Route::currentRouteName(), ['welcome', 'register', 'password.request', 'password.reset']))
    @include('layouts.sidebar')
@endif


        <div class="page-wrapper">
            {{-- Navbar / Top Navigation (jika tidak di halaman otentikasi) --}}
            @if (!in_array(Route::currentRouteName(), ['login', 'register', 'password.request', 'password.reset']))
                @include('layouts.navigation') {{-- Ini adalah navbar utama Anda --}}
            @endif

            {{-- Page Header (opsional, untuk judul halaman dan breadcrumbs) --}}
            {{-- Anda bisa mengaktifkan ini jika ingin memiliki judul halaman yang konsisten --}}
            {{-- <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <div class="page-pretitle">
                                @yield('page_pretitle')
                            </div>
                            <h2 class="page-title">
                                @yield('page_title', 'Dashboard')
                            </h2>
                        </div>
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                @yield('page_actions')
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="page-body">
                <div class="container-xl px-3"> {{-- Menggunakan container-xl untuk konten utama --}}
                    @yield('content')
                </div>
            </div>

            {{-- @include('layouts.footer') --}}
        </div>
    </div>

    {{-- Menggunakan versi spesifik yang sama dengan CSS --}}
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</body>
</html>