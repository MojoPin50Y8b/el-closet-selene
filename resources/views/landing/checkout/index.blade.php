@extends('landing.layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8 grid md:grid-cols-3 gap-6">
        {{-- Columna principal --}}
        <section class="md:col-span-2 space-y-6">
            {{-- Datos de envío --}}
            <div class="bg-white rounded-xl border p-4">
                <h2 class="font-semibold mb-3">Datos de envío</h2>

                <form method="POST" action="{{ route('shop.checkout.place') }}" id="place-order-form" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input name="name" class="border rounded px-3 py-2" placeholder="Nombre completo" required>
                        <input name="phone" class="border rounded px-3 py-2" placeholder="Teléfono" required>
                        <input name="email" type="email" class="border rounded px-3 py-2 md:col-span-2" placeholder="Email"
                            required>
                        <input name="zip" class="border rounded px-3 py-2" placeholder="C.P." required>
                        <input name="city" class="border rounded px-3 py-2" placeholder="Ciudad" required>
                        <input name="state" class="border rounded px-3 py-2" placeholder="Estado/Provincia" required>
                        <input name="address" class="border rounded px-3 py-2 md:col-span-2"
                            placeholder="Dirección completa" required>
                        <textarea name="notes" class="border rounded px-3 py-2 md:col-span-2" rows="3"
                            placeholder="Notas para la entrega (opcional)"></textarea>
                    </div>
                </form>
            </div>

            {{-- Cupón --}}
            <div class="bg-white rounded-xl border p-4">
                <h2 class="font-semibold mb-3">Cupón</h2>
                <form method="POST" action="{{ route('shop.checkout.coupon') }}" class="flex gap-2">
                    @csrf
                    <input name="code" class="border rounded px-3 py-2 flex-1" placeholder="Código de descuento">
                    <button class="px-4 py-2 rounded border">Aplicar</button>
                </form>
                @if(session('status'))
                    <p class="text-sm mt-2">{{ session('status') }}</p>
                @endif
            </div>
        </section>

        {{-- Resumen --}}
        <aside>
            <div class="bg-white rounded-xl border p-4">
                <h3 class="font-semibold mb-3">Resumen</h3>

                @if(($items ?? collect())->count())
                    <ul class="divide-y mb-3 max-h-80 overflow-auto">
                        @foreach($items as $it)
                            <li class="py-2 flex justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm line-clamp-1">{{ $it->product_name }}</div>
                                    <div class="text-xs text-gray-500">× {{ $it->qty }}</div>
                                </div>
                                <div class="text-right">
                                    ${{ number_format($it->price * $it->qty, 2) }}
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <p class="flex justify-between font-medium">
                        <span>Total</span>
                        <span>${{ number_format($total ?? 0, 2) }}</span>
                    </p>

                    <button form="place-order-form" class="btn-primary w-full mt-4 py-2 rounded">
                        Realizar pedido
                    </button>
                @else
                    <p class="text-gray-600">Tu carrito está vacío.</p>
                    <a href="{{ route('shop.search') }}" class="mt-3 inline-block px-4 py-2 rounded border">Ir a comprar</a>
                @endif
            </div>
        </aside>
    </div>
@endsection