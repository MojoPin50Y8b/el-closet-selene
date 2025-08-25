@extends('landing.layouts.app')

@section('title', $category->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold">{{ $category->name }}</h1>
            {{-- aquí más adelante agregaremos filtros/sort --}}
        </div>

        @if($products->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <x-landing.product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <p class="text-oslo-gray">No hay productos en esta categoría.</p>
        @endif
    </div>
@endsection