<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet"/>
</head>
<body class="d-flex flex-column">
    {{-- Include navigation only if not on auth pages --}}
        @include('layouts.navigation')


    {{-- Include sidebar only if not on auth pages --}}
    @if (!in_array(Route::currentRouteName(), ['login', 'register', 'password.request', 'password.reset']))
        @include('layouts.sidebar')
    @endif

    <div class="page">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <script src="https://unpkg.com/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>