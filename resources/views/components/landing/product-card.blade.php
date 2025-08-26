@props(['product'])

@php
    $img = optional($product->images->first())->url ?? '';

    // precio "desde": mínimo de la variante (sale_price | price)
    $variant = $product->variants->sortBy(function ($v) {
        return $v->sale_price ?? $v->price;
    })->first();

    $price = $variant ? ($variant->sale_price ?? $variant->price)
        : ($product->sale_price ?? $product->price ?? 0);

    $firstVariantId = optional($variant)->id;
@endphp

<div class="bg-white rounded-xl border overflow-hidden">
    <a href="{{ route('shop.product', $product->slug) }}" class="block">
        <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full aspect-[4/5] object-cover bg-gray-100">
    </a>
    <div class="p-3">
        <h3 class="text-sm mb-1 line-clamp-1">{{ $product->name }}</h3>
        <p class="font-semibold mb-3">${{ number_format($price, 2) }}</p>

        <div class="flex gap-2">
            <a href="{{ route('shop.product', $product->slug) }}" class="px-3 py-1 border rounded">Ver</a>

            {{-- Botón añadir (si hay variantes, manda la primera por defecto) --}}
            <button class="js-add-to-cart px-3 py-1 rounded btn-primary" data-url="{{ route('shop.cart.add') }}"
                data-product="{{ $product->id }}" @if($firstVariantId) data-variant="{{ $firstVariantId }}" @endif
                data-qty="1">
                Añadir
            </button>
        </div>
    </div>
</div>