@extends('layouts.app')

@section('content')
    @include('partials.buyer-header', ['activePage' => 'catalog'])

    <main class="page-section">
        <div class="grid gap-6 xl:grid-cols-[1.08fr,0.92fr]">
            <section class="overflow-hidden rounded-[36px] border border-slate-200 bg-white shadow-[0_24px_60px_-35px_rgba(15,34,90,0.24)]">
                <div class="bg-gradient-to-br {{ $product['tone'] }} p-8 text-white">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="page-kicker text-white/70">{{ $product['type'] }}</p>
                            <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight lg:text-5xl">{{ $product['name'] }}</h1>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-white/85">{{ $product['description'] }}</p>
                        </div>
                        <div class="flex h-28 w-28 items-center justify-center rounded-[28px] bg-white/10 text-4xl font-black">
                            {{ $product['thumb'] }}
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 p-6 lg:grid-cols-[1fr,0.95fr] lg:p-8">
                    <div>
                        <p class="page-kicker">Ringkasan produk</p>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div class="surface-card-muted">
                                <p class="eyebrow-label">Game</p>
                                <p class="mt-2 text-lg font-extrabold text-slate-900">{{ $product['game'] }}</p>
                            </div>
                            <div class="surface-card-muted">
                                <p class="eyebrow-label">Region</p>
                                <p class="mt-2 text-lg font-extrabold text-slate-900">{{ $product['region'] }}</p>
                            </div>
                            <div class="surface-card-muted">
                                <p class="eyebrow-label">Stok</p>
                                <p class="mt-2 text-lg font-extrabold text-slate-900">{{ $product['stock'] }}</p>
                            </div>
                            <div class="surface-card-muted">
                                <p class="eyebrow-label">Delivery</p>
                                <p class="mt-2 text-lg font-extrabold text-slate-900">{{ $product['delivery'] }}</p>
                            </div>
                        </div>

                        <div class="mt-6 surface-card-muted">
                            <p class="page-kicker">Tag pencarian</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($product['tags'] as $tag)
                                    <span class="rounded-full bg-white px-3 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="surface-card-dark">
                        <p class="page-kicker text-blue-200">Seller card</p>
                        <h2 class="mt-3 text-2xl font-extrabold text-white">{{ $product['seller'] }}</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                                <p class="eyebrow-label text-slate-300">Rating</p>
                                <p class="mt-2 text-lg font-extrabold text-white">{{ $product['rating'] }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                                <p class="eyebrow-label text-slate-300">Terjual</p>
                                <p class="mt-2 text-lg font-extrabold text-white">{{ number_format($product['sold_count'], 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 rounded-[26px] border border-white/10 bg-white/10 p-5">
                            <p class="text-sm text-blue-100/80">
                                Buyer akan melihat detail seller, harga, stok, dan alur checkout langsung dari halaman ini.
                            </p>
                        </div>

                        <div class="mt-6 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.24em] text-blue-100/70">Harga</p>
                                <p class="mt-2 text-3xl font-black text-white">{{ $product['price'] }}</p>
                            </div>
                            @auth
                                <a class="primary-btn px-5 py-3" href="{{ route('checkout.show', $productModel) }}">Checkout</a>
                            @else
                                <a class="primary-btn px-5 py-3" href="{{ route('login') }}">Login untuk beli</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <div class="surface-card">
                    <p class="page-kicker">Checkout Flow</p>
                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        <div class="surface-card-muted">1. Buyer cek detail listing dan reputasi seller.</div>
                        <div class="surface-card-muted">2. Isi game user ID, jumlah, dan metode pembayaran.</div>
                        <div class="surface-card-muted">3. Transaksi otomatis masuk ke history buyer dan panel seller.</div>
                    </div>
                </div>

                <div class="surface-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="page-kicker">Produk terkait</p>
                            <h2 class="mt-2 text-2xl font-extrabold text-slate-900">Buyer juga melihat</h2>
                        </div>
                        <a class="subtle-link" href="{{ route('catalog') }}">Semua</a>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($relatedProducts as $relatedProduct)
                            @include('partials.product-card', ['product' => $relatedProduct])
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </main>

    @include('partials.site-footer')
@endsection
