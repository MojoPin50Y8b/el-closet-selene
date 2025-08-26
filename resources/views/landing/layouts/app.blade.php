<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Necesario para fetch POST (lo lee app.js) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'El Clóset de Selene')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --gondola: #1b1116;
            --razzmatazz: #df0f7b;
            --alabaster: #fbfbfb;
            --oslo-gray: #9a9b9c;
            --silver-sand: #c8cacb;
            --pink-swan: #bfb1ba;
            --lola: #dfd0d9;
            --persian-rose: #fc249c;
            --friar-gray: #7c7c74;
            --mist-gray: #bcbdb4;
        }

        body {
            background: var(--alabaster);
            color: var(--gondola)
        }

        .btn-primary {
            background: var(--razzmatazz);
            color: #fff
        }

        .badge-sale {
            background: var(--persian-rose);
            color: #fff
        }

        a {
            color: var(--razzmatazz)
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    @include('landing.partials.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('landing.partials.footer')
</body>

</html>