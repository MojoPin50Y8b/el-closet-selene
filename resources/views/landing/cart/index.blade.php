@extends('landing.layouts.app')

@section('title', 'Carrito')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold mb-6">Tu carrito</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Lista de productos --}}
            <section class="lg:col-span-2 space-y-4" id="js-cart-lines">
                @forelse ($items as $i)
                    <article class="bg-white rounded-xl border p-4 flex gap-4 items-center" data-cart-row data-id="{{ $i->id }}"
                        data-price="{{ (float) $i->price }}" data-qty="{{ (int) $i->qty }}">
                        <img src="{{ $i->image_url ?: 'https://via.placeholder.com/120x120?text=Producto' }}"
                            alt="Imagen de {{ $i->product_name }}" class="w-24 h-24 rounded object-cover bg-gray-100">

                        <div class="flex-1">
                            <a href="{{ route('shop.product', $i->product_slug) }}" class="font-medium hover:underline">
                                {{ $i->product_name }}
                            </a>
                            <div class="text-sm text-gray-500 mt-1">
                                Cantidad: <span class="js-line-qty">{{ (int) $i->qty }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-semibold">
                                $<span class="js-line-total" data-unit="{{ number_format((float) $i->price, 2, '.', '') }}"
                                    data-qty="{{ (int) $i->qty }}">{{ number_format($i->price * $i->qty, 2) }}</span>
                            </div>

                            <button class="text-sm text-red-600 hover:underline mt-2" data-remove-from-cart
                                data-id="{{ $i->id }}" data-url="{{ route('shop.cart.remove') }}"
                                aria-label="Quitar {{ $i->product_name }}">
                                Quitar
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border bg-white p-6 text-gray-600">
                        Tu carrito está vacío.
                    </div>
                @endforelse
            </section>

            {{-- Resumen --}}
            <aside class="lg:col-span-1">
                <div class="bg-gray-50 border rounded-xl p-5">
                    <h2 class="font-semibold text-lg mb-4">Resumen</h2>

                    <div class="flex justify-between py-2">
                        <span>Subtotal</span>
                        <span>$<span id="js-cart-subtotal">{{ number_format($total, 2) }}</span></span>
                    </div>
                    <div class="flex justify-between py-2 border-t">
                        <span>Envío</span>
                        <span class="text-gray-500">Calculado en checkout</span>
                    </div>
                    <div class="flex justify-between py-3 border-t font-semibold text-lg">
                        <span>Total</span>
                        <span>$<span id="js-cart-total">{{ number_format($total, 2) }}</span></span>
                    </div>

                    <a href="{{ route('shop.checkout') }}"
                        class="block text-center w-full mt-4 bg-razzmatazz text-white py-2.5 rounded hover:opacity-90">
                        Continuar al pago
                    </a>
                    <a href="{{ route('shop.home') }}"
                        class="block text-center w-full mt-2 border py-2.5 rounded hover:bg-gray-50">
                        Seguir comprando
                    </a>
                </div>
            </aside>
        </div>
    </div>
@endsection