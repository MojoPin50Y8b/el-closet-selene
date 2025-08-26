@props(['product'])

@php
    $img = optional($product->images->first())->url ?? '';
    $url = route('shop.product', ['slug' => $product->slug]);
    $price = optional($product->variants->first())->sale_price
        ?? optional($product->variants->first())->price
        ?? ($product->price ?? null);
@endphp

<article class="border rounded-xl overflow-hidden">
    <a href="{{ $url }}" class="block aspect-[4/5] bg-silver-sand/40">
        @if($img)
            <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @endif
    </a>
    <div class="p-3">
        <a href="{{ $url }}" class="block font-medium mb-1 line-clamp-1">{{ $product->name }}</a>
        @if($price)
            <div class="text-sm mb-3">${{ number_format($price, 2) }}</div>
        @endif

        <div class="flex items-center gap-2">
            <a href="{{ $url }}" class="px-3 py-2 border rounded">Ver</a>
            <button class="btn-primary px-3 py-2 rounded js-add-to-cart" data-url="{{ route('shop.cart.add') }}"
                data-product="{{ $product->id }}" data-qty="1">
                Añadir
            </button>
        </div>
    </div>
</article>