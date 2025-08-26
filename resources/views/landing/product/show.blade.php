@extends('landing.layouts.app')

@section('title', $product->name)

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-8 grid md:grid-cols-2 gap-8">

        {{-- GALERÍA --}}
        <div>
            @php $imgs = $product->images ?? collect(); @endphp
            @if($imgs->count())
                <div class="aspect-[4/5] rounded-xl overflow-hidden border">
                    <img src="{{ $imgs->first()->url ?? '' }}" alt="{{ $product->name }}" class="w-full h-full object-cover"
                        id="main-img">
                </div>
                <div class="mt-3 grid grid-cols-5 gap-2">
                    @foreach($imgs as $img)
                        <button type="button" class="border rounded overflow-hidden thumb" data-src="{{ $img->url }}">
                            <img src="{{ $img->url }}" alt="" class="w-full h-16 object-cover">
                        </button>
                    @endforeach
                </div>
            @else
                <div class="aspect-[4/5] rounded-xl bg-silver-sand/40"></div>
            @endif
        </div>

        {{-- INFO / VARIANTES --}}
        <div>
            <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>

            {{-- Precio (simple; ajusta si usas precios en variantes) --}}
            @php
                $base = optional($product->variants->first())->price ?? $product->price ?? null;
                $sale = optional($product->variants->first())->sale_price ?? null;
            @endphp

            <div class="mt-2">
                @if($sale)
                    <span class="text-2xl font-semibold mr-2">${{ number_format($sale, 2) }}</span>
                    <span class="text-friar-gray line-through">${{ number_format($base, 2) }}</span>
                    <span class="ml-2 text-persian-rose font-medium">Sale</span>
                @elseif($base)
                    <span class="text-2xl font-semibold">${{ number_format($base, 2) }}</span>
                @endif
            </div>

            {{-- Selección de variante (si aplica) --}}
            @if($product->variants->count())
                <label class="block mt-6 text-sm text-gray-600">Variante</label>
                <select id="variant-select" class="border rounded w-full p-2">
                    @foreach($product->variants as $v)
                        @php
                            $label = $v->values->map(function ($vv) {
                                return ($vv->attribute->name ?? '') . ': ' . ($vv->value->name ?? '');
                            })->join(' / ');
                            $label = $label ?: 'Default';
                            $price = $v->sale_price ?? $v->price;
                        @endphp
                        <option value="{{ $v->id }}" data-price="{{ $price }}">
                            {{ $label }} — ${{ number_format($price, 2) }}
                        </option>
                    @endforeach
                </select>
            @endif

            <div class="mt-4 flex items-center gap-3">
                <input id="qty-input" type="number" min="1" value="1" class="w-20 border rounded p-2">
                <button class="btn-primary px-4 py-2 rounded js-add-to-cart" data-url="{{ route('shop.cart.add') }}"
                    data-product="{{ $product->id }}" data-variant-el="#variant-select" data-qty-el="#qty-input">
                    Añadir al carrito
                </button>
                <button class="px-4 py-2 rounded border">❤ Wishlist</button>
            </div>

            {{-- DESCRIPCIÓN --}}
            @if($product->description)
                <div class="prose mt-6 max-w-none">{!! nl2br(e($product->description)) !!}</div>
            @endif

            {{-- RELACIONADOS --}}
            @if($related->count())
                <h2 class="mt-10 mb-4 text-lg font-semibold">También te puede gustar</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($related as $rp)
                        <x-landing.product-card :product="$rp" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- mini script para thumbs (sin dependencias) --}}
    <script>
        document.querySelectorAll('.thumb').forEach(btn => {
            btn.addEventListener('click', () => {
                const src = btn.dataset.src;
                const main = document.getElementById('main-img');
                if (src && main) main.src = src;
            });
        });
    </script>
@endsection