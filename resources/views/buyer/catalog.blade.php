@extends('layouts.app')

@section('content')
    @include('partials.buyer-header', ['activePage' => 'catalog'])

    <main class="pb-10">
        <section class="page-section">
            <div class="page-hero">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                    <div class="max-w-2xl">
                        <p class="page-kicker">Katalog Buyer</p>
                        <h1 class="page-title">Cari item, voucher, akun, dan game key</h1>
                        <p class="page-copy">
                            Halaman ini melanjutkan flow buyer dari homepage: filter sederhana, daftar produk rapih, dan informasi
                            seller yang langsung terlihat tanpa harus membuka banyak layar.
                        </p>
                    </div>

                    <form action="{{ route('catalog') }}" class="grid gap-3 md:grid-cols-[1.8fr,1fr,auto] xl:min-w-[540px]" method="GET">
                        <input
                            class="field-input min-w-[260px] bg-white/95"
                            name="q"
                            placeholder="Cari berdasarkan nama game atau produk"
                            type="text"
                            value="{{ $searchQuery }}"
                        >
                        <select class="field-select bg-white/95" name="type">
                            <option value="">Semua tipe</option>
                            @foreach ($productTypes as $type)
                                <option value="{{ $type }}" @selected($selectedType === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        <button class="primary-btn h-[52px] justify-center px-6" type="submit">Terapkan</button>
                    </form>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($quickCategories as $category)
                        <a
                            class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $selectedType === $category['filter'] ? 'bg-brand-700 text-white shadow-[0_18px_34px_-24px_rgba(31,99,213,0.7)]' : 'bg-white/85 text-brand-700 ring-1 ring-slate-200 hover:bg-brand-50' }}"
                            href="{{ route('catalog', ['type' => $category['filter']]) }}"
                        >
                            {{ $category['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="page-section pt-0 pb-16">
            <div class="grid gap-6 xl:grid-cols-[1.55fr,0.75fr]">
                <div>
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="page-kicker text-slate-500">Hasil</p>
                            <p class="mt-1 text-xl font-extrabold text-slate-900">{{ count($products) }} produk ditemukan</p>
                        </div>
                        @if ($searchQuery !== '' || $selectedType !== '')
                            <a class="subtle-link" href="{{ route('catalog') }}">Reset filter</a>
                        @endif
                    </div>

                    @if (count($products) > 0)
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($products as $product)
                                @include('partials.product-card', ['product' => $product])
                            @endforeach
                        </div>
                        
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="empty-state-card text-center">
                            <p class="page-kicker">Tidak ada hasil</p>
                            <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Coba keyword atau kategori lain.</h2>
                            <p class="mt-4 text-sm leading-7 text-slate-600">
                                Anda bisa reset filter, gunakan nama game utama, atau pilih kategori populer dari chip di atas.
                            </p>
                        </div>
                    @endif
                </div>

                <aside class="space-y-4">
                    <div class="surface-card-dark">
                        <p class="page-kicker text-blue-200">Flow Buyer</p>
                        <h2 class="mt-3 font-display text-2xl font-extrabold text-white">Alur pembelian yang cocok untuk item game</h2>
                        <div class="mt-6 space-y-4 text-sm text-slate-200">
                            <div class="rounded-[22px] border border-white/10 bg-white/10 p-4">1. Buyer cari item dari header atau kategori populer.</div>
                            <div class="rounded-[22px] border border-white/10 bg-white/10 p-4">2. Buyer buka detail produk untuk cek seller, stok, dan deskripsi.</div>
                            <div class="rounded-[22px] border border-white/10 bg-white/10 p-4">3. Checkout membuat transaksi baru dan seller bisa update status order.</div>
                        </div>
                    </div>

                    <div class="surface-card">
                        <p class="page-kicker">Butuh seller panel?</p>
                        <h2 class="section-title">UI buyer ini sudah dipasangkan dengan dashboard penjual.</h2>
                        <p class="section-copy">
                            Jadi Anda punya dua sisi utama marketplace sejak awal: storefront untuk transaksi dan panel operasional seller.
                        </p>
                        <a class="primary-btn mt-6 w-full justify-center px-5 py-4" href="{{ route('seller.dashboard') }}">
                            Lihat dashboard penjual
                        </a>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    @include('partials.site-footer')
@endsection
