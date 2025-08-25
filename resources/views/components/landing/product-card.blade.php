@props(['product'])

<a href="{{ route('shop.product', ['slug' => $product->slug ?? 'producto-demo']) }}"
    class="block rounded-xl border p-3 hover:shadow">
    <div class="aspect-[4/5] bg-silver-sand/40 rounded mb-3 overflow-hidden">
        @if(!empty($product->images[0]?->url))
            <img src="{{ $product->images[0]->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @endif
    </div>
    <div class="text-sm text-friar-gray">{{ $product->brand->name ?? 'Marca' }}</div>
    <div class="font-medium">{{ $product->name ?? 'Producto' }}</div>
    <div class="text-persian-rose font-semibold">
        ${{ number_format($product->price ?? 0, 2) }}
    </div>
</a>