@extends('landing.layouts.app')

@section('title', $category->name)

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold">{{ $category->name }}</h1>

            {{-- (Opcional) orden + filtros simples a futuro --}}
            <form method="GET" class="flex items-center gap-3">
                <select name="sort" class="border rounded p-2">
                    <option value="">Ordenar</option>
                    <option value="new" @selected(request('sort') === 'new')>Novedades</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Precio ↑</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Precio ↓</option>
                </select>
                <button class="btn-primary px-3 py-2 rounded">Aplicar</button>
            </form>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @forelse($products as $p)
                <x-landing.product-card :product="$p" />
            @empty
                <p class="col-span-full text-friar-gray">Aún no hay productos en esta categoría.</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </section>
@endsection