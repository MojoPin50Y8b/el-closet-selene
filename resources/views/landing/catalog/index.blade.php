@extends('landing.layouts.app')

@section('title', $title ?? 'Búsqueda')

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold">
                {{ $title ?? 'Búsqueda' }}
            </h1>

            {{-- atajo opcional para refinar --}}
            <form action="{{ route('shop.search') }}" method="GET" class="hidden md:block">
                <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Buscar productos…"
                    class="border rounded-xl px-4 py-2 outline-none focus:ring">
            </form>
        </div>

        @if(($products ?? null) && $products->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <x-landing.product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <p class="text-friar-gray">No encontramos resultados.</p>
        @endif
    </section>
@endsection