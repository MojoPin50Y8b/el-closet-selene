@extends('landing.layouts.app')

@section('title', $product->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8 grid md:grid-cols-2 gap-8">
        {{-- Galería --}}
        <div>
            @php
                $main = $product->images->first();
            @endphp

            <div class="aspect-[4/5] bg-silver-sand/30 rounded-xl overflow-hidden mb-4">
                @if($main)
                    <img src="{{ $main->url ?? $main->image_url }}" alt="{{ $product->name }}"
                        class="w-full h-full object-cover">
                @endif
            </div>

            @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images->skip(1) as $img)
                        <div class="aspect-[4/5] rounded overflow-hidden bg-silver-sand/30">
                            <img src="{{ $img->url ?? $img->image_url }}" class="w-full h-full object-cover" alt="">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold mb-2">{{ $product->name }}</h1>

            @php
                $minPrice = $product->variants->min('price');
                $minSale = $product->variants->whereNotNull('sale_price')->min('sale_price');
            @endphp

            <div class="mb-4">
                @if($minSale)
                    <span class="text-2xl font-bold text-persian-rose mr-2">${{ number_format($minSale, 2) }}</span>
                    <span class="text-oslo-gray line-through">${{ number_format($minPrice, 2) }}</span>
                @else
                    <span class="text-2xl font-bold">${{ number_format($minPrice, 2) }}</span>
                @endif
            </div>

            <p class="text-friar-gray mb-6">{{ $product->short_description ?? '' }}</p>

            {{-- Formulario Añadir al carrito --}}
            <form method="POST" action="{{ route('shop.cart.add') }}" class="space-y-4" data-cart-add>
                @csrf

                {{-- Variante: mostramos un solo select compuesto (size/color) --}}
                @if($product->variants->count())
                    <label class="block text-sm mb-1">Variante</label>
                    <select name="variant_id" class="border rounded px-3 py-2 w-full" required>
                        @foreach($product->variants as $v)
                            @php
                                $label = $v->values->map(fn($vv) => $vv->attribute->name . ': ' . $vv->value->name)->join(' · ');
                                $price = $v->sale_price ?? $v->price;
                            @endphp
                            <option value="{{ $v->id }}">
                                {{ $label ?: 'Variante ' . $v->id }} — ${{ number_format($price, 2) }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <div class="flex items-center gap-3">
                    <label class="text-sm">Cantidad</label>
                    <input type="number" name="qty" value="1" min="1" class="border rounded px-3 py-2 w-24">
                </div>

                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="flex gap-3">
                    <button type="submit" class="bg-black text-white px-6 py-3 rounded hover:opacity-90">
                        Añadir al carrito
                    </button>

                    {{-- Wishlist (placeholder) --}}
                    <button type="button" class="border px-4 py-3 rounded text-oslo-gray" disabled title="Próximamente">
                        ♥ Wishlist
                    </button>
                </div>
            </form>

            {{-- Productos relacionados --}}
            @if($related->count())
                <h2 class="mt-10 mb-4 text-lg font-semibold">También te puede interesar</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($related as $rp)
                        <x-landing.product-card :product="$rp" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection