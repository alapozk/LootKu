@extends('layouts.app')

@section('content')
    @include('partials.buyer-header', ['activePage' => 'home'])

    <main class="pb-10">
        <section class="shell py-8">
            <div class="hero-surface relative overflow-hidden rounded-[38px] px-5 py-6 shadow-[0_30px_90px_-35px_rgba(15,34,90,0.45)] sm:px-8 sm:py-8">
                <div class="absolute -right-16 top-0 hidden h-72 w-72 rounded-full bg-white/35 blur-3xl lg:block"></div>
                <div class="absolute bottom-0 right-24 hidden h-56 w-56 rounded-full bg-cyan-300/35 blur-3xl lg:block"></div>

                <div class="relative grid gap-6 xl:grid-cols-[320px,1fr]">
                    <div class="rounded-[30px] bg-slate-950/90 p-6 text-white shadow-xl shadow-brand-950/30">
                        <p class="text-sm font-semibold uppercase tracking-[0.34em] text-blue-200">Buyer storefront</p>
                        <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight">
                            Marketplace item game dengan nuansa seperti referensi Anda.
                        </h1>
                        <p class="mt-4 text-sm leading-7 text-blue-100/80">
                            Fokus utama di halaman buyer ini adalah search yang dominan, promo card besar, kategori cepat, dan
                            produk paling ramai agar flow belanja terasa familiar untuk user marketplace game.
                        </p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                            @foreach ($marketStats as $stat)
                                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4">
                                    <p class="text-xs uppercase tracking-[0.26em] text-blue-100/60">{{ $stat['label'] }}</p>
                                    <p class="mt-2 text-2xl font-black">{{ $stat['value'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 space-y-3 text-sm text-blue-100/80">
                            @foreach ($sellerPerks as $perk)
                                <div class="flex gap-3">
                                    <span class="mt-1 flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 text-[11px] font-black text-white">+</span>
                                    <p>{{ $perk }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($promoCards as $card)
                            <article class="relative flex min-h-[390px] flex-col justify-between overflow-hidden rounded-[30px] border border-white/20 bg-gradient-to-br {{ $card['tone'] }} p-6 text-white shadow-xl shadow-brand-950/20">
                                <div class="absolute -right-10 top-10 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                                <div class="relative space-y-4">
                                    <span class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-bold uppercase tracking-[0.24em]">
                                        {{ $card['tag'] }}
                                    </span>
                                    <p class="text-xs uppercase tracking-[0.34em] text-white/60">{{ $card['headline'] }}</p>
                                    <h2 class="font-display text-4xl font-extrabold leading-none">{{ $card['title'] }}</h2>
                                    <p class="max-w-[18ch] text-sm leading-6 text-white/80">{{ $card['subtitle'] }}</p>
                                </div>

                                <div class="relative space-y-4">
                                    <p class="text-3xl font-black">{{ $card['offer'] }}</p>
                                    <a class="secondary-btn w-full justify-center px-4 py-3" href="{{ route('catalog', ['type' => $card['type']]) }}">
                                        {{ $card['cta'] }}
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="relative mt-6 flex flex-wrap items-center justify-center gap-3 rounded-[24px] bg-brand-600/90 px-4 py-4 text-white shadow-lg shadow-brand-900/20">
                    @foreach ($trustSignals as $signal)
                        <div class="flex items-center gap-3 rounded-full bg-white/10 px-4 py-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-xs font-black tracking-[0.28em]">
                                {{ $signal['icon'] }}
                            </span>
                            <span class="text-sm font-semibold">{{ $signal['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="shell py-8">
            <div class="rounded-[34px] bg-white p-6 shadow-[0_24px_60px_-35px_rgba(15,34,90,0.35)] ring-1 ring-slate-200 lg:p-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.34em] text-brand-700">Kategori Cepat</p>
                        <h2 class="mt-2 font-display text-3xl font-extrabold text-slate-900">Shortcut untuk buyer yang mau belanja cepat</h2>
                    </div>
                    <a class="text-sm font-bold text-brand-700 transition hover:text-brand-800" href="{{ route('catalog') }}">Lihat semua kategori</a>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8">
                    @foreach ($quickCategories as $category)
                        <a class="group rounded-[26px] bg-slate-50 p-4 text-center transition hover:-translate-y-1 hover:bg-brand-50 hover:shadow-lg hover:shadow-brand-100/60" href="{{ route('catalog', ['type' => $category['filter']]) }}">
                            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-[20px] bg-gradient-to-br from-brand-600 to-brand-400 font-black text-white shadow-lg shadow-brand-200">
                                {{ $category['icon'] }}
                            </span>
                            <p class="mt-4 font-bold text-slate-900">{{ $category['label'] }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $category['desc'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="shell py-8">
            <div class="overflow-hidden rounded-[38px] bg-gradient-to-br from-brand-950 via-brand-800 to-brand-700 text-white shadow-[0_30px_90px_-35px_rgba(15,34,90,0.65)]">
                <div class="flex flex-col gap-5 border-b border-white/10 px-6 py-7 lg:flex-row lg:items-end lg:justify-between lg:px-8">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.34em] text-blue-200">Beli Cepat</p>
                        <h2 class="mt-3 font-display text-3xl font-extrabold">Produk yang paling ramai dicari buyer</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-blue-100/80">
                            Grid ini meniru pola marketplace item game: produk unggulan langsung terlihat, informasi harga jelas,
                            dan CTA tetap dekat dengan keputusan beli.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a class="rounded-full bg-white px-4 py-2 text-sm font-bold text-brand-700" href="{{ route('catalog') }}">Semua</a>
                        @foreach (array_slice($quickCategories, 0, 5) as $category)
                            <a class="rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20" href="{{ route('catalog', ['type' => $category['filter']]) }}">
                                {{ $category['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="px-6 py-6 lg:px-8">
                    @if ($featuredProductsCount > 0)
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            @foreach ($featuredProducts as $product)
                                @include('partials.product-card', ['product' => $product])
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-[30px] border border-white/10 bg-white/10 p-8 text-center">
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-blue-200">Belum ada listing</p>
                            <h3 class="mt-3 font-display text-3xl font-extrabold text-white">Marketplace ini masih kosong.</h3>
                            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-blue-100/80">
                                Seller belum menambahkan produk apa pun. Buyer bisa kembali nanti, atau Anda bisa daftar sebagai seller
                                dan membuat listing pertama dari dashboard.
                            </p>
                            <div class="mt-6 flex flex-wrap justify-center gap-3">
                                @guest
                                    <a class="primary-btn px-5 py-3" href="{{ route('register') }}">Daftar sebagai seller</a>
                                @endguest
                                <a class="secondary-btn px-5 py-3 text-white" href="{{ route('catalog') }}">Buka katalog</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="shell py-8">
            <div class="grid gap-6 xl:grid-cols-[1.2fr,0.8fr]">
                <div class="rounded-[34px] bg-white p-6 shadow-[0_24px_60px_-35px_rgba(15,34,90,0.35)] ring-1 ring-slate-200 lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.34em] text-brand-700">Buyer Experience</p>
                    <h2 class="mt-3 font-display text-3xl font-extrabold text-slate-900">Apa yang sudah siap di MVP ini</h2>

                    <div class="mt-8 grid gap-4 md:grid-cols-3">
                        <article class="rounded-[26px] bg-slate-50 p-5">
                            <p class="text-sm font-bold text-brand-700">Search first</p>
                            <h3 class="mt-3 text-xl font-extrabold text-slate-900">Header fokus pencarian</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                Buyer bisa mulai dari keyword, tag populer, atau langsung masuk kategori yang paling sering dicari.
                            </p>
                        </article>
                        <article class="rounded-[26px] bg-slate-50 p-5">
                            <p class="text-sm font-bold text-brand-700">Promo card</p>
                            <h3 class="mt-3 text-xl font-extrabold text-slate-900">Banner visual yang padat</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                Area promo besar membantu mengangkat campaign top up, game key baru, dan item musiman.
                            </p>
                        </article>
                        <article class="rounded-[26px] bg-slate-50 p-5">
                            <p class="text-sm font-bold text-brand-700">Trust layer</p>
                            <h3 class="mt-3 text-xl font-extrabold text-slate-900">Sinyal aman di setiap fold</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                Escrow, refund, dan dukungan CS ditonjolkan supaya buyer cepat percaya sebelum checkout.
                            </p>
                        </article>
                    </div>
                </div>

                <div class="rounded-[34px] bg-slate-950 p-6 text-white shadow-[0_24px_70px_-35px_rgba(15,34,90,0.65)] lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.34em] text-blue-200">Seller Side</p>
                    <h2 class="mt-3 font-display text-3xl font-extrabold">Dashboard penjual juga sudah disiapkan.</h2>
                    <p class="mt-4 text-sm leading-7 text-blue-100/80">
                        Penjual mendapatkan panel khusus untuk memantau omzet, order aktif, performa listing, dan riwayat saldo
                        supaya workflow jual beli item game terasa lengkap.
                    </p>

                    <div class="mt-6 space-y-3">
                        @foreach ($sellerPerks as $perk)
                            <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-sm text-blue-100/80">
                                {{ $perk }}
                            </div>
                        @endforeach
                    </div>

                    <a class="primary-btn mt-8 w-full justify-center px-5 py-4" href="{{ route('seller.dashboard') }}">
                        Buka dashboard penjual
                    </a>
                </div>
            </div>
        </section>
    </main>

    @include('partials.site-footer')
@endsection
