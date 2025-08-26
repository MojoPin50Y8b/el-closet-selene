@extends('landing.layouts.app')

@section('title', 'Inicio')

@section('content')
    {{-- HERO / SLIDER --}}
    <section class="max-w-7xl mx-auto px-4 py-10">
        @if(isset($banners) && $banners->count())
            <div x-data="{ i: 0, t: null, total: {{ $banners->count() }} }"
                 x-init="t = setInterval(() => { i = (i + 1) % total }, 5000)"
                 class="relative overflow-hidden rounded-2xl">
                @foreach ($banners as $k => $b)
                    <a href="{{ $b->url ?? route('shop.sale') }}" class="block" x-show="i === {{ $k }}" x-transition.opacity x-cloak>
                        <img src="{{ $b->image_url }}" alt="{{ $b->title ?? 'Banner' }}" class="w-full h-64 md:h-96 object-cover">
                        <div class="absolute inset-0 bg-black/30 flex items-end">
                            <div class="p-6 text-white">
                                @if($b->title)
                                    <h2 class="text-3xl md:text-5xl font-serif">{{ $b->title }}</h2>
                                @endif
                                @if($b->subtitle)
                                    <p class="mt-2">{{ $b->subtitle }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach

                {{-- Controles / indicadores --}}
                <div class="absolute inset-x-0 bottom-3 flex justify-center gap-2">
                    @foreach ($banners as $k => $b)
                        <button class="w-2.5 h-2.5 rounded-full" :class="i === {{ $k }} ? 'bg-white' : 'bg-white/50'"
                                @click="i = {{ $k }}" aria-label="Ir al banner {{ $k + 1 }}"></button>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Fallback estático si no hay banners --}}
            <div class="bg-lola rounded-2xl p-10 text-center overflow-hidden">
                <h1 class="text-4xl md:text-5xl font-serif mb-4">New Collections</h1>
                <p class="text-oslo-gray mb-6">Rebajas de temporada · Envíos a todo México y Sudamérica</p>
                <a href="{{ route('shop.sale') }}"
                   class="inline-block bg-persian-rose text-white px-6 py-3 rounded shadow hover:opacity-90">
                    Ver Ofertas
                </a>
            </div>
        @endif
    </section>

    {{-- CATEGORÍAS DESTACADAS --}}
    <section class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold">Categorías destacadas</h2>
            <a href="{{ route('shop.search', ['filter' => 'categories']) }}" class="text-sm underline">Ver todas</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @forelse(($categories ?? []) as $cat)
                <a href="{{ route('shop.category', ['slug' => $cat->slug]) }}"
                   class="h-28 rounded-xl bg-silver-sand/40 hover:bg-silver-sand/60 flex items-center justify-center">
                    {{ $cat->name }}
                </a>
            @empty
                @foreach ([
                    'Hombres'     => 'hombres',
                    'Mujeres'     => 'mujeres',
                    'Niños/Niñas' => 'ninos-ninas',
                    'Accesorios'  => 'accesorios',
                ] as $label => $slug)
                    <a href="{{ route('shop.category', ['slug' => $slug]) }}"
                       class="h-28 rounded-xl bg-silver-sand/40 hover:bg-silver-sand/60 flex items-center justify-center">
                        {{ $label }}
                    </a>
                @endforeach
            @endforelse
        </div>
    </section>

    {{-- NOVEDADES --}}
    <section class="max-w-7xl mx-auto px-4 mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold">Novedades</h2>
            <a href="{{ route('shop.search', ['sort' => 'new']) }}" class="text-sm underline">Ver más</a>
        </div>

        @if(isset($newProducts) && $newProducts->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($newProducts as $product)
                    <x-landing.product-card :product="$product" />
                @endforeach
            </div>
        @else
            {{-- placeholders si aún no hay data --}}
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

    {{-- OFERTAS / SALE --}}
    <section class="max-w-7xl mx-auto px-4 mt-10 mb-16">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold">
                <span class="px-2 py-1 rounded bg-persian-rose text-white text-sm mr-2">SALE</span>
                Ofertas
            </h2>
        <a href="{{ route('shop.sale') }}" class="text-sm underline">Ver todas las ofertas</a>
        </div>

        @if(isset($saleProducts) && $saleProducts->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($saleProducts as $product)
                    <x-landing.product-card :product="$product" />
                @endforeach
            </div>
        @else
            <p class="text-friar-gray">Aún no hay productos en oferta.</p>
        @endif
    </section>
@endsection
