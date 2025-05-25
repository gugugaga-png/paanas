<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tabler CSS via CDN -->
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet"/>
</head>
<body class="d-flex flex-column">
    @include('layouts.navigation')

    <div class="page">

           <div class="container">
             @yield('content')
           </div>

    </div>

    <!-- Tabler JS via CDN -->
    <script src="https://unpkg.com/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>
