@props(['product'])

@php
    // Compatibilidad: usa claves comunes si vienen desde Eloquent
    $name = $product->name ?? ($product['name'] ?? 'Producto');
    $price = $product->price ?? ($product['price'] ?? 0);
    $sale = $product->sale_price ?? ($product['sale_price'] ?? null);
    $img = $product->image_url
        ?? (isset($product->images[0]) ? $product->images[0]->url : null)
        ?? ($product['image_url'] ?? null);
@endphp

<article class="rounded-xl border hover:shadow-sm transition">
    <a href="{{ isset($product->slug) ? route('landing.product.show', $product->slug) : '#' }}" class="block p-3">
        <div class="relative">
            <div class="aspect-[4/5] bg-silver-sand/40 rounded overflow-hidden">
                @if($img)
                    <img src="{{ $img }}" alt="{{ $name }}" class="w-full h-full object-cover">
                @endif
            </div>
            @if($sale && $sale < $price)
                <span class="absolute top-2 left-2 text-xs px-2 py-1 rounded bg-razzmatazz text-white">SALE</span>
            @endif
        </div>

        <h3 class="mt-3 text-sm line-clamp-2">{{ $name }}</h3>

        <div class="mt-1">
            @if($sale && $sale < $price)
                <span class="font-semibold">MXN {{ number_format($sale, 2) }}</span>
                <span class="text-oslo-gray line-through ml-2 text-sm">MXN {{ number_format($price, 2) }}</span>
            @else
                <span class="font-semibold">MXN {{ number_format($price, 2) }}</span>
            @endif
        </div>

        <div class="mt-3">
            <button type="button" class="w-full bg-gondola text-white py-2 rounded hover:opacity-90">
                Añadir al carrito
            </button>
        </div>
    </a>
</article>