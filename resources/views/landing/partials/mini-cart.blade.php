<div class="p-3 w-80">
  @if($items->count())
    <ul class="divide-y">
      @foreach($items as $i)
        <li class="py-2 flex items-center justify-between">
          <a class="text-sm line-clamp-1" href="{{ route('shop.product', $i->product_slug) }}">
            {{ $i->product_name }}
          </a>
          <div class="text-right">
            <div class="text-sm">x{{ $i->qty }}</div>
            <div class="font-semibold">${{ number_format($i->qty * (float)$i->price, 2) }}</div>
          </div>
        </li>
      @endforeach
    </ul>
    <div class="mt-3 flex items-center justify-between">
      <span class="text-sm text-gray-500">Total</span>
      <span class="font-semibold">${{ number_format($total, 2) }}</span>
    </div>
    <a href="{{ route('shop.cart') }}" class="mt-3 w-full inline-block text-center btn-primary px-3 py-2 rounded">
      Ir al carrito
    </a>
  @else
    <p class="text-sm text-gray-600">Tu carrito está vacío.</p>
  @endif
</div>
