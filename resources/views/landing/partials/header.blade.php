<header class="border-b bg-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">
        <a href="{{ route('shop.home') }}" class="text-xl font-bold">EL CLÓSET DE SELENE</a>
        <nav class="ml-auto hidden md:flex gap-6">
            <a href="{{ route('shop.category', ['slug' => 'hombre']) }}">Hombres</a>
            <a href="{{ route('shop.category', ['slug' => 'mujer']) }}">Mujeres</a>
            <a href="{{ route('shop.category', ['slug' => 'ninos-ninas']) }}">Niños/Niñas</a>
            <a href="{{ route('shop.category', ['slug' => 'accesorios']) }}">Accesorios</a>
            <a href="{{ route('shop.new') }}">Nuevos</a>
            <a href="{{ route('shop.sale') }}">Ofertas</a>
        </nav>
        <a href="{{ route('shop.cart') }}" class="ml-4">Carrito</a>

        @auth
            <a href="{{ route('admin.dashboard') }}" class="ml-2">Panel</a>
            <form method="POST" action="{{ route('logout') }}" class="ml-2">@csrf<button>Salir</button></form>
        @else
            <a href="{{ route('login') }}" class="ml-2">Ingresar</a>
        @endauth
    </div>
</header>