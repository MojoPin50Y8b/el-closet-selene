<header class="border-b bg-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">
        {{-- Logo / Home --}}
        <a href="{{ route('landing.home') }}" class="text-xl font-bold tracking-wide">
            EL CLÓSET DE SELENE
        </a>

        {{-- Menú principal (md+) --}}
        <nav class="ml-auto hidden md:flex gap-6">
            <a href="{{ route('landing.catalog') }}" class="hover:text-razzmatazz">Hombres</a>
            <a href="{{ route('landing.catalog') }}" class="hover:text-razzmatazz">Mujeres</a>
            <a href="{{ route('landing.catalog') }}" class="hover:text-razzmatazz">Niños/Niñas</a>
            <a href="{{ route('landing.catalog') }}" class="hover:text-razzmatazz">Accesorios</a>
            <a href="{{ route('landing.new') }}" class="hover:text-razzmatazz">Nuevos</a>
            <a href="{{ route('landing.sale') }}" class="px-2 py-1 rounded bg-persian-rose text-white">Ofertas</a>
        </nav>

        {{-- Acciones --}}
        <div class="flex items-center gap-3">
            {{-- (Opcional) búsqueda básica; más adelante la conectamos a un controlador --}}
            <form action="#" method="get" class="hidden lg:block">
                <input type="search" name="q" placeholder="Buscar..."
                    class="border rounded px-3 py-1 w-56 focus:outline-none focus:ring-2 focus:ring-persian-rose/40">
            </form>

            <a href="{{ route('cart.index') }}" class="ml-2 relative">
                <span class="material-symbols-outlined align-middle">shopping_cart</span>
                <span id="cart-count"
                    class="absolute -top-2 -right-2 text-xs bg-razzmatazz text-white rounded-full px-1">
                    0
                </span>
            </a>

            @auth
                <a href="{{ route('admin.dashboard') }}" class="ml-2">Panel</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="ml-2 hover:text-razzmatazz">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="ml-2">Ingresar</a>
                <a href="{{ route('register') }}" class="ml-1">Crear cuenta</a>
            @endauth
        </div>
    </div>
</header>