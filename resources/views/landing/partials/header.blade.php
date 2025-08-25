<header class="border-b bg-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">
        <a href="{{ route('landing.home') }}" class="text-xl font-bold">EL CLÓSET DE SELENE</a>
        <nav class="ml-auto hidden md:flex gap-6">
            <a href="{{ route('landing.catalog') }}">Hombres</a>
            <a href="{{ route('landing.catalog') }}">Mujeres</a>
            <a href="{{ route('landing.catalog') }}">Niños/Niñas</a>
            <a href="{{ route('landing.catalog') }}">Accesorios</a>
            <a href="{{ route('landing.new') }}">Nuevos</a>
            <a href="{{ route('landing.sale') }}" class="badge-sale px-2 py-1 rounded">Ofertas</a>
        </nav>
        <a href="{{ route('cart.index') }}" class="ml-4">🛒 <span id="cart-count">0</span></a>
        @auth
            <a href="{{ route('admin.dashboard') }}" class="ml-2">Panel</a>
            <form method="POST" action="{{ route('logout') }}" class="ml-2">@csrf
                <button>Salir</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="ml-2">Ingresar</a>
        @endauth
    </div>
</header>