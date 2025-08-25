<header class="bg-white border-b p-4 flex items-center justify-between">
    <div class="font-semibold">@yield('title', 'Dashboard')</div>
    <div class="flex items-center gap-3">
        <span>{{ auth()->user()->name }}</span>
        <form action="{{ route('logout') }}" method="POST">@csrf<button>Salir</button></form>
    </div>
</header>