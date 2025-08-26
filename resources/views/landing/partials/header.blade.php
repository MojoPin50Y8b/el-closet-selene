<header class="border-b bg-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">
        {{-- LOGO --}}
        <a href="{{ route('shop.home') }}" class="text-xl font-bold whitespace-nowrap">
            EL CLÓSET DE SELENE
        </a>

        {{-- BÚSQUEDA (desktop) --}}
        <div class="relative flex-1 hidden md:block">
            <form action="{{ route('shop.search') }}" method="GET" class="relative">
                <input id="search-input" name="q" type="search" autocomplete="off" placeholder="Buscar productos…"
                    class="w-full border rounded-xl px-4 py-2 outline-none focus:ring" />
            </form>

            {{-- Panel de sugerencias --}}
            <div id="search-panel" class="absolute left-0 right-0 mt-1 bg-white border rounded-xl shadow-lg hidden">
                <ul id="search-results" class="divide-y"></ul>
            </div>
        </div>

        {{-- MENÚ --}}
        <nav class="ml-auto hidden md:flex gap-4">
            {{-- Slugs unificados (plural) --}}
            <a href="{{ route('shop.category', ['slug' => 'hombres']) }}">Hombres</a>
            <a href="{{ route('shop.category', ['slug' => 'mujeres']) }}">Mujeres</a>
            <a href="{{ route('shop.category', ['slug' => 'ninos-ninas']) }}">Niños/Niñas</a>
            <a href="{{ route('shop.category', ['slug' => 'accesorios']) }}">Accesorios</a>
            <a href="{{ route('shop.new') }}">Nuevos</a>
            <a href="{{ route('shop.sale') }}" class="text-pink-600 font-semibold">Ofertas</a>
        </nav>

        {{-- ICONOS --}}
        <div class="flex items-center gap-3">
            {{-- Cuenta --}}
            @auth
                <a href="{{ route('profile.edit') }}" title="Mi cuenta" class="p-2 rounded hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}" title="Ingresar" class="p-2 rounded hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                    </svg>
                </a>
            @endauth

            {{-- Wishlist (placeholder) --}}
            <button type="button" title="Wishlist (próximamente)"
                class="p-2 rounded hover:bg-gray-100 opacity-60 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 3 13.197 3 10.75 3 8.264 4.794 6.5 7.125 6.5c1.3 0 2.475.57 3.375 1.473A4.74 4.74 0 0113.875 6.5C16.206 6.5 18 8.264 18 10.75c0 2.447-1.688 4.61-3.989 6.757a25.175 25.175 0 01-4.244 3.17 14.978 14.978 0 01-.383.218l-.022.012-.007.004-.003.001-.001.001a.75.75 0 01-.682 0l-.001-.001-.003-.001z" />
                </svg>
            </button>

            {{-- Carrito --}}
            <a href="{{ route('shop.cart') }}" class="relative p-2 rounded hover:bg-gray-100" title="Carrito">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835L5.25 6.75M7.5 14.25h8.87c.86 0 1.61-.586 1.81-1.425l1.2-5a1.125 1.125 0 00-1.094-1.375H6.128M7.5 14.25L5.25 6.75M7.5 14.25L6 18m9 0H7.5m7.5 0l1.5-3.75M6 18a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm12 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                </svg>
                <span id="cart-count"
                    class="absolute -top-1 -right-1 text-xs bg-pink-600 text-white rounded-full px-1.5 py-0.5">0</span>
            </a>
        </div>
    </div>
</header>