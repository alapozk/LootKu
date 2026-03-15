<article class="group overflow-hidden rounded-[30px] border border-slate-200/90 bg-white/95 shadow-[0_24px_60px_-38px_rgba(15,23,42,0.24)] transition hover:-translate-y-1 hover:shadow-[0_28px_70px_-40px_rgba(15,23,42,0.3)]">
    <div class="relative h-40 overflow-hidden bg-gradient-to-br {{ $product['tone'] }} p-5 text-white">
        <div class="absolute inset-x-6 bottom-0 h-px bg-white/20"></div>
        <div class="relative flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/70">{{ $product['game'] }}</p>
                <p class="mt-4 text-4xl font-black">{{ $product['thumb'] }}</p>
            </div>
            <span class="rounded-full border border-white/15 bg-white/15 px-3 py-1 text-xs font-bold backdrop-blur-sm">{{ $product['highlight'] }}</span>
        </div>
    </div>

    <div class="space-y-5 p-5">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="eyebrow-label">{{ $product['type'] }}</p>
                <h3 class="mt-2 line-clamp-2 text-xl font-extrabold tracking-tight text-slate-900">{{ $product['name'] }}</h3>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">{{ $product['rating'] }}</span>
        </div>

        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <span class="rounded-full bg-slate-100 px-3 py-2 text-slate-600">{{ $product['seller'] }}</span>
            <span class="rounded-full bg-slate-100 px-3 py-2 text-slate-600">{{ $product['region'] }}</span>
        </div>

        <div class="grid grid-cols-2 gap-3 rounded-[22px] border border-slate-200 bg-slate-50/90 p-3 text-sm text-slate-600">
            <div>
                <p class="eyebrow-label">Stok</p>
                <p class="mt-1 font-bold text-slate-900">{{ $product['stock'] }}</p>
            </div>
            <div>
                <p class="eyebrow-label">Pengiriman</p>
                <p class="mt-1 font-bold text-slate-900">{{ $product['delivery'] }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 text-sm text-slate-500">
            <span>{{ number_format($product['sold_count'] ?? 0, 0, ',', '.') }} terjual</span>
            <span class="truncate text-right">{{ count($product['tags'] ?? []) > 0 ? implode(' • ', array_slice($product['tags'], 0, 2)) : 'Siap dibeli' }}</span>
        </div>

        <div class="flex items-end justify-between gap-4 border-t border-slate-200 pt-4">
            <div>
                <p class="eyebrow-label">Mulai dari</p>
                <p class="mt-1 text-2xl font-black text-brand-700">{{ $product['price'] }}</p>
            </div>
            <a class="primary-btn px-4 py-3 text-sm" href="{{ route('products.show', $product['slug']) }}">
                Detail produk
            </a>
        </div>
    </div>
</article>
