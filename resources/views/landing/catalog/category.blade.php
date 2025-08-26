@extends('landing.layouts.app')

@section('title', $category->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-6">
            {{-- Sidebar filtros --}}
            <aside class="md:w-64 shrink-0">
                <form method="GET" action="{{ route('shop.category', ['slug' => $category->slug]) }}" class="space-y-6">
                    {{-- Precio --}}
                    <div class="p-4 bg-white rounded-xl border">
                        <h3 class="font-semibold mb-3">Precio</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min" class="w-1/2 border rounded px-3 py-2"
                                placeholder="{{ (int) ($range->min_price ?? 0) }}" value="{{ old('min', $filters['min']) }}">
                            <span>-</span>
                            <input type="number" name="max" class="w-1/2 border rounded px-3 py-2"
                                placeholder="{{ (int) ($range->max_price ?? 0) }}" value="{{ old('max', $filters['max']) }}">
                        </div>
                    </div>

                    {{-- Talla (si hay) --}}
                    @if($sizes->count())
                        <div class="p-4 bg-white rounded-xl border">
                            <h3 class="font-semibold mb-3">Talla</h3>
                            <select name="size" class="w-full border rounded px-3 py-2">
                                <option value="">Todas</option>
                                @foreach($sizes as $s)
                                    <option value="{{ $s->slug }}" @selected($filters['size'] === $s->slug)>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Color (si hay) --}}
                    @if($colors->count())
                        <div class="p-4 bg-white rounded-xl border">
                            <h3 class="font-semibold mb-3">Color</h3>
                            <select name="color" class="w-full border rounded px-3 py-2">
                                <option value="">Todos</option>
                                @foreach($colors as $c)
                                    <option value="{{ $c->slug }}" @selected($filters['color'] === $c->slug)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Orden simple --}}
                    <div class="p-4 bg-white rounded-xl border">
                        <h3 class="font-semibold mb-3">Ordenar</h3>
                        <select name="sort" class="w-full border rounded px-3 py-2">
                            <option value="">Relevancia</option>
                            <option value="new" @selected($filters['sort'] === 'new')>Novedades</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button class="btn-primary px-4 py-2 rounded">Aplicar</button>
                        <a href="{{ route('shop.category', $category->slug) }}" class="px-4 py-2 rounded border">
                            Limpiar
                        </a>
                    </div>
                </form>
            </aside>

            {{-- Listado --}}
            <section class="flex-1">
                <div class="flex items-baseline justify-between mb-4">
                    <h1 class="text-2xl font-semibold">{{ $category->name }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ $products->total() }} resultado{{ $products->total() === 1 ? '' : 's' }}
                    </p>
                </div>

                @if($products->count())
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <x-landing.product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="rounded-xl border bg-white p-6 text-center text-gray-600">
                        No hay productos que coincidan con los filtros.
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection