@props(['product'])

@php
    $img = $product->images->first()->url ?? 'https://placehold.co/600x750';
    $url = route('shop.product', ['slug' => $product->slug]);
@endphp

<a href="{{ $url }}" class="block rounded-xl border p-3 hover:shadow">
    <div class="aspect-[4/5] bg-silver-sand/30 rounded mb-3 overflow-hidden">
        <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
    </div>
    <div class="text-sm text-oslo-gray">{{ $product->brand->name ?? '' }}</div>
    <div class="font-medium">{{ $product->name }}</div>

    @if(!empty($product->sale_price))
        <div class="mt-1">
            <span class="font-semibold text-persian-rose">${{ number_format($product->sale_price, 2) }}</span>
            <span class="text-friar-gray line-through ml-2">${{ number_format($product->price, 2) }}</span>
        </div>
    @else
        <div class="mt-1 font-semibold">${{ number_format($product->price ?? 0, 2) }}</div>
    @endif
</a>