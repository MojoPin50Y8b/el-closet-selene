<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'El Clóset de Selene')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-alabaster text-gondola antialiased">

    @include('landing.partials.header')

    <main class="min-h-[60vh]">
        @yield('content')
    </main>

    @include('landing.partials.footer')

</body>

</html>