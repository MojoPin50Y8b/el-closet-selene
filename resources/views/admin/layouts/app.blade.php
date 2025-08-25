<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin · El Clóset de Selene')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="min-h-screen grid grid-cols-[260px_1fr]">
        @include('admin.partials.sidebar')
        <div>
            @include('admin.partials.topbar')
            <main class="p-6">@yield('content')</main>
        </div>
    </div>
</body>

</html>