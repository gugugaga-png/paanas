<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Meta dan stylesheet sama -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet"/>
    <style>
    .container-landing {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    @media (min-width: 992px) {
        .container-landing {
            padding-left: 6rem;
            padding-right: 6rem;
        }
    }

    @media (min-width: 1200px) {
        .container-landing {
            padding-left: 10rem;
            padding-right: 10rem;
        }
    }
</style>

</head>
<body class="layout-fluid">
    <div class="page">
        @include('layouts.navigation') {{-- Navbar khusus untuk landing page --}}

        {{-- Bagian utama halaman --}}
        <div class="page-wrapper">
            <div class="page-body">
                {{-- Ganti jadi container khusus landing --}}
                <div class="container-xl container-landing">

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
