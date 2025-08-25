@extends('landing.layouts.app')

@section('title', 'Inicio')

@section('content')
    {{-- HERO --}}
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="bg-lola rounded-2xl p-10 text-center overflow-hidden">
            <h1 class="text-4xl md:text-5xl font-serif mb-4">New Collections</h1>
            <p class="text-oslo-gray mb-6">
                Rebajas de temporada · Envíos a todo México y Sudamérica
            </p>
            <a href="{{ route('shop.sale') }}"
                class="inline-block bg-persian-rose text-white px-6 py-3 rounded shadow hover:opacity-90">
                Ver Ofertas
            </a>
        </div>
    </section>

    {{-- CATEGORÍAS DESTACADAS --}}
    <section class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl font-semibold mb-4">Categorías destacadas</h2>

        @php use Illuminate\Support\Str; @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach (['Hombre', 'Mujer', 'Niños Niñas', 'Accesorios'] as $cat)
                <a href="{{ route('shop.category', ['slug' => Str::slug($cat)]) }}"
                    class="h-28 rounded-xl bg-silver-sand/40 hover:bg-silver-sand/60 flex items-center justify-center">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </section>

    {{-- NOVEDADES --}}
    <section class="max-w-7xl mx-auto px-4 mt-10">
        <h2 class="text-2xl font-semibold mb-4">Novedades</h2>

        @if(isset($newProducts) && $newProducts->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($newProducts as $product)
                    <x-landing.product-card :product="$product" />
                @endforeach
            </div>
        @else
            {{-- placeholders si aún no pasas datos desde el controlador --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @for ($i = 0; $i < 4; $i++)
                    <div class="rounded-xl border p-3">
                        <div class="aspect-[4/5] bg-silver-sand/40 rounded mb-3"></div>
                        <div class="h-4 bg-silver-sand/70 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-silver-sand/70 rounded w-1/3"></div>
                    </div>
                @endfor
            </div>
        @endif
    </section>

    {{-- VALORES DE MARCA --}}
    <section class="max-w-7xl mx-auto px-4 mt-12 mb-16">
        <div class="grid md:grid-cols-3 gap-4">
            <div class="rounded-xl border p-6 text-center">
                <p class="text-lg font-semibold mb-1">Envío Gratis</p>
                <p class="text-sm text-friar-gray">en pedidos desde $999 MXN</p>
            </div>
            <div class="rounded-xl border p-6 text-center">
                <p class="text-lg font-semibold mb-1">Devoluciones 30 días</p>
                <p class="text-sm text-friar-gray">compra sin preocupaciones</p>
            </div>
            <div class="rounded-xl border p-6 text-center">
                <p class="text-lg font-semibold mb-1">Pago 100% Seguro</p>
                <p class="text-sm text-friar-gray">tarjeta, PayPal y más</p>
            </div>
        </div>
    </section>
@endsection