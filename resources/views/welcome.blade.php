@extends('layouts.landing')

@section('content')
<div class="container-md py-5 px-4">
    {{-- Hero Section --}}
    <div class="row align-items-center py-5 py-md-6 py-lg-7" style="min-height: 80vh;">
        <div class="col-md-6 text-center text-md-start mb-4 mb-md-0">
            <h1 class="fw-bolder display-4 display-lg-3 mb-3">Menabung Lebih Teratur Dengan Sapphy</h1>
            <p class="lead mb-4">
                Sapphy membantu Anda mengelola keuangan pribadi dengan mudah. Capai tujuan tabungan Anda dengan fitur-fitur inovatif kami.
            </p>
            <div class="d-grid gap-2 d-md-block">
                <a href="{{ route('login') }}" class="btn btn-lg btn-dark me-md-2 mb-2 mb-md-0 rounded-pill">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-lg btn-outline-dark rounded-pill">Register</a>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <img src="{{ asset('images/phone.png') }}" alt="Ilustrasi Aplikasi Mobile Sapphy" class="img-fluid">
        </div>
    </div>

    <hr class="my-5">

    {{-- Fitur 1 - Milestone --}}
    <div class="row align-items-center py-5 py-md-6" style="min-height: 70vh;">
        <div class="col-md-6 text-center text-md-start mb-4 mb-md-0">
            <h2 class="fw-bold display-5 display-lg-4 mb-3">Capai Tujuan Keuangan Anda dengan Fitur Milestone Baru!</h2>
            <p class="lead">
                Fitur Milestone kami memungkinkan Anda menetapkan target tabungan yang jelas dan melacak kemajuan Anda secara visual. Rayakan setiap pencapaian kecil menuju kebebasan finansial.
            </p>
        </div>
        <div class="col-md-6 text-center">
            <img src="{{ asset('images/chart.png') }}" alt="Ilustrasi Bagan Pengeluaran" class="img-fluid">
        </div>
    </div>

    <hr class="my-5">

    {{-- Fitur 2 - Ringkasan Tahunan --}}
    <div class="row align-items-center py-5 py-md-6" style="min-height: 70vh;">
        <div class="col-md-7 order-md-2 text-center text-md-start mb-4 mb-md-0">
            <h2 class="fw-bold display-5 display-lg-4 mb-3">Dapatkan Ringkasan Keuangan Mingguan Anda</h2>
            <p class="lead">
                Sapphy menyediakan ringkasan pengeluaran dan pemasukan mingguan yang komprehensif. Pahami kebiasaan finansial Anda dan buat keputusan yang lebih cerdas untuk masa depan.
            </p>
        </div>
        <div class="col-md-5 order-md-1 text-center">
            <img src="{{ asset('images/statchart.svg') }}" alt="Ilustrasi Ringkasan Tahunan" class="img-fluid">
        </div>
    </div>

    <hr class="my-5">

    {{-- FAQ --}}
    <section class="py-5 py-md-6" style="min-height: 70vh;">
        <h2 class="text-center mb-5 display-5 fw-bold">Pertanyaan Umum (FAQ)</h2> {{-- Consistent heading size --}}
        <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item border rounded-3 mb-2"> {{-- Added border and rounded corners for visual appeal --}}
                <h3 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        Apa itu Sapphy?
                    </button>
                </h3>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Sapphy adalah platform yang dirancang untuk membantu Anda menabung dan mengelola keuangan pribadi dengan lebih teratur dan efisien. Anda dapat bergabung dengan 'segmen tabungan' yang berbeda untuk tujuan keuangan spesifik Anda.
                    </div>
                </div>
            </div>

            <div class="accordion-item border rounded-3 mb-2">
                <h3 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Bagaimana cara bergabung dengan segmen tabungan?
                    </button>
                </h3>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Anda dapat bergabung dengan segmen tabungan menggunakan kode unik yang diberikan oleh guru atau administrator Anda. Masuk ke akun Anda dan cari opsi "Gabung Segmen" di dasbor Anda.
                    </div>
                </div>
            </div>

            <div class="accordion-item border rounded-3 mb-2">
                <h3 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Apakah data tabungan saya aman?
                    </button>
                </h3>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Ya, kami sangat serius dalam menjaga keamanan data Anda. Sapphy menggunakan praktik keamanan terbaik untuk melindungi informasi pribadi dan keuangan Anda.
                    </div>
                </div>
            </div>

            <div class="accordion-item border rounded-3 mb-2">
                <h3 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Bisakah saya menarik uang kapan saja?
                    </button>
                </h3>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Untuk penarikan dana, Anda perlu mengajukan permintaan penarikan yang kemudian akan divalidasi oleh guru atau administrator segmen Anda. Setelah disetujui, dana akan dapat ditarik.
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Footer --}}
<footer class="footer py-4 bg-dark text-white-50">
    <div class="container text-center">
        <div class="row align-items-center justify-content-between">
            <div class="col-12 col-md-auto mb-3 mb-md-0">
                &copy; {{ date('Y') }} Sapphy. Dibuat dengan ❤️ oleh Group 4
            </div>
            <div class="col-12 col-md-auto">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        <a href="#" class="link-secondary text-decoration-none">Kebijakan Privasi</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#" class="link-secondary text-decoration-none">Ketentuan Layanan</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
@endsection
