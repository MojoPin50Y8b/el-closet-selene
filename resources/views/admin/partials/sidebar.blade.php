<aside class="bg-white border-r p-4">
    <h1 class="font-bold text-lg mb-6">El Clóset · Admin</h1>
    <nav class="grid gap-2 text-sm">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.products.index') }}">Productos</a>
        <a href="{{ route('admin.orders.index') }}">Pedidos</a>
        <a href="{{ route('admin.categories.index') }}">Categorías</a>
        <a href="{{ route('admin.coupons.index') }}">Cupones</a>
        <a href="{{ route('admin.banners.index') }}">Banners</a>
    </nav>
</aside>