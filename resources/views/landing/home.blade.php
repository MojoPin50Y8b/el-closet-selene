@extends('landing.layouts.app')
@section('title', 'Inicio')
@section('content')
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="mb-10 rounded-2xl overflow-hidden">
            <div class="bg-[var(--lola)] p-10 text-center">
                <h1 class="text-4xl md:text-5xl font-serif mb-4">New Collections</h1>
                <p class="text-gray-600 mb-6">Rebajas de temporada · Envíos a todo México y Sudamérica</p>
                <a href="{{ route('landing.sale') }}" class="btn-primary px-6 py-3 rounded">Comprar Ofertas</a>
            </div>
        </div>

        <h2 class="text-2xl font-semibold mb-4">Novedades</h2>
        {{-- aquí iteras productos destacados --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            {{-- <x-product-card :product="$p" /> --}}
            <div class="border rounded p-3">Producto demo</div>
        </div>
    </section>
@endsection