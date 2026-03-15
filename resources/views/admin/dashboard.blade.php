@php
    $bodyClass = 'bg-[#f4f5fb] text-slate-900';
    $admin = auth()->user();
@endphp
@extends('layouts.app')

@section('content')
    <div class="softdash-shell min-h-screen">
        <div class="mx-auto flex max-w-[1500px] gap-4 px-4 py-5 sm:px-5 lg:px-6 xl:px-8">
            <aside class="softdash-sidebar hidden lg:flex lg:flex-col">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[18px] bg-amber-50 text-base font-black text-amber-700">
                            AD
                        </div>
                        <div>
                            <p class="font-display text-3xl font-extrabold tracking-tight text-slate-900">Lootku</p>
                            <p class="text-sm text-slate-400">Admin control</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-white px-3 py-2 text-xs font-semibold text-slate-500 shadow-sm">admin</span>
                </div>

                <div class="mt-8 space-y-8">
                    <div>
                        <p class="softdash-label">Monitoring</p>
                        <div class="mt-3 space-y-2">
                            <a class="softdash-nav-link softdash-nav-link-active" href="{{ route('admin.dashboard') }}">
                                <span>Overview</span>
                                <span class="text-xs font-semibold">aktif</span>
                            </a>
                            <a class="softdash-nav-link" href="{{ route('transactions.index') }}">Riwayat akun</a>
                            <a class="softdash-nav-link" href="{{ route('home') }}">Buyer storefront</a>
                        </div>
                    </div>
                </div>

                <div class="mt-auto rounded-[24px] border border-slate-200 bg-white p-5 shadow-[0_18px_35px_-28px_rgba(15,23,42,0.2)]">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-50 text-sm font-bold text-amber-700">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-slate-900">{{ $admin->name }}</p>
                            <p class="truncate text-sm text-slate-400">{{ $admin->email }}</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        <div class="metric-line">
                            <span>Total buyer</span>
                            <span class="font-semibold text-slate-900">{{ $dashboardStats[0]['value'] }}</span>
                        </div>
                        <div class="metric-line">
                            <span>Total seller</span>
                            <span class="font-semibold text-slate-900">{{ $dashboardStats[1]['value'] }}</span>
                        </div>
                        <div class="metric-line">
                            <span>Produk aktif</span>
                            <span class="font-semibold text-slate-900">{{ $dashboardStats[2]['value'] }}</span>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="min-w-0 flex-1">
                <div class="space-y-5">
                    <div class="flex items-center justify-between lg:hidden">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Admin Dashboard</p>
                            <h1 class="mt-2 font-display text-3xl font-extrabold text-slate-900">Control center</h1>
                        </div>
                        <span class="softdash-pill">admin</span>
                    </div>

                    <div class="softdash-mobile-nav">
                        <a class="softdash-mobile-link" href="{{ route('admin.dashboard') }}">Overview</a>
                        <a class="softdash-mobile-link" href="{{ route('transactions.index') }}">Riwayat</a>
                        <a class="softdash-mobile-link" href="{{ route('home') }}">Storefront</a>
                    </div>

                    <section class="page-hero">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <p class="page-kicker text-amber-700">Dashboard Admin</p>
                                <h1 class="page-title">Marketplace monitoring yang rapi dan lebih mudah dipindai</h1>
                                <p class="page-copy">
                                    Buyer, seller, listing, stok kritis, dan transaksi terbaru sekarang tersusun dalam pola yang sama
                                    dengan workspace seller, jadi panel backoffice terasa lebih konsisten.
                                </p>
                            </div>

                            <div class="page-actions">
                                <a class="toolbar-pill" href="{{ route('home') }}">Lihat storefront</a>
                                <a class="toolbar-pill" href="{{ route('transactions.index') }}">Buka riwayat akun</a>
                            </div>
                        </div>
                    </section>

                    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($dashboardStats as $stat)
                            <article class="softdash-stat">
                                <p class="text-sm text-slate-400">{{ $stat['label'] }}</p>
                                <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $stat['value'] }}</p>
                                <p class="mt-3 text-sm leading-6 text-slate-500">{{ $stat['note'] }}</p>
                            </article>
                        @endforeach
                    </section>

                    <!-- Modul Withdrawals & Disputes -->
                    <section class="grid gap-5 2xl:grid-cols-[1fr,1fr]">
                        <!-- Withdrawals -->
                        <article class="softdash-card p-6 lg:p-7">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="page-kicker text-amber-700">Financial Ops</p>
                                    <h2 class="section-title">Antrean Penarikan Saldo</h2>
                                </div>
                                <span class="softdash-pill">{{ $withdrawals->where('status', 'Pending')->count() }} pending</span>
                            </div>

                            <div class="mt-6 space-y-3">
                                @forelse ($withdrawals as $w)
                                    @php
                                        $wStatusClass = match($w->status) {
                                            'Pending' => 'bg-amber-100 text-amber-800',
                                            'Sukses' => 'bg-emerald-100 text-emerald-800',
                                            'Ditolak' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-slate-100 text-slate-800'
                                        };
                                    @endphp
                                    <div class="rounded-[20px] border border-slate-200 bg-white p-4">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $w->user->store_name ?: $w->user->name }}</p>
                                                <p class="text-sm text-slate-500">{{ $w->bank_name }} - {{ $w->bank_account }}</p>
                                                <p class="text-xs text-slate-400 mt-1">{{ $w->created_at->format('d M Y H:i') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-lg text-slate-900">Rp {{ number_format($w->amount, 0, ',', '.') }}</p>
                                                <span class="inline-block mt-1 rounded-full px-2 py-1 text-xs font-bold {{ $wStatusClass }}">{{ $w->status }}</span>
                                            </div>
                                        </div>
                                        @if($w->status === 'Pending')
                                        <div class="mt-4 flex gap-2">
                                            <form action="{{ route('admin.withdrawals.update', $w) }}" method="POST" class="flex-1">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="Sukses">
                                                <button class="w-full rounded-[12px] bg-emerald-600 px-3 py-2 text-sm font-bold text-white hover:bg-emerald-700">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.withdrawals.update', $w) }}" method="POST" class="flex-1">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="Ditolak">
                                                <button class="w-full rounded-[12px] bg-rose-600 px-3 py-2 text-sm font-bold text-white hover:bg-rose-700">Reject</button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="empty-state-card mt-6">Belum ada request penarikan saldo.</div>
                                @endforelse
                            </div>
                        </article>

                        <!-- Disputes -->
                        <article class="softdash-card p-6 lg:p-7">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="page-kicker text-rose-700">Resolution Center</p>
                                    <h2 class="section-title">Pusat Komplain Transaksi</h2>
                                </div>
                                <span class="softdash-pill bg-rose-100 text-rose-800">{{ $disputedTransactions->count() }} kasus aktif</span>
                            </div>

                            <div class="mt-6 space-y-3">
                                @forelse ($disputedTransactions as $d)
                                    <div class="rounded-[20px] border border-rose-200 bg-rose-50/50 p-4">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <a href="{{ route('transactions.show', $d) }}" class="font-bold text-rose-900 hover:underline">{{ $d->reference }}</a>
                                                <p class="text-sm font-medium text-slate-800 mt-1">{{ $d->product_name }}</p>
                                                <p class="text-xs text-slate-500 mt-1">Buyer: {{ $d->buyer->name }} | Seller: {{ $d->seller->store_name ?: $d->seller->name }}</p>
                                                <p class="text-xs text-slate-400 mt-1">Diupdate: {{ $d->updated_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-slate-900 mb-2">Rp {{ number_format($d->total, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex gap-2">
                                            <form action="{{ route('admin.disputes.resolve', $d) }}" method="POST" class="flex-1">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="resolution" value="seller">
                                                <button class="w-full rounded-[12px] bg-indigo-600 px-3 py-2 text-sm font-bold text-white hover:bg-indigo-700">Menangkan Seller</button>
                                            </form>
                                            <form action="{{ route('admin.disputes.resolve', $d) }}" method="POST" class="flex-1">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="resolution" value="buyer">
                                                <button class="w-full rounded-[12px] object-cover bg-rose-600 px-3 py-2 text-sm font-bold text-white hover:bg-rose-700">Refund Buyer (Batal)</button>
                                            </form>
                                        </div>
                                        <div class="mt-2 text-center">
                                            <a href="{{ route('transactions.show', $d) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Cek Chat & Bukti &rarr;</a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state-card mt-6">Tidak ada transaksi yang dikomplain.</div>
                                @endforelse
                            </div>
                        </article>
                    </section>

                    <section class="grid gap-5 2xl:grid-cols-[1.36fr,0.84fr]">
                        <article class="softdash-card p-6 lg:p-7">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="page-kicker text-amber-700">Transaksi Terkini</p>
                                    <h2 class="section-title">Order terbaru seluruh marketplace</h2>
                                </div>
                                <span class="softdash-pill">{{ $recentTransactions->count() }} order</span>
                            </div>

                            @if ($recentTransactions->count() > 0)
                                <div class="mt-6 space-y-3 lg:hidden">
                                    @foreach ($recentTransactions as $transaction)
                                        @php
                                            $statusClass = match ($transaction->status) {
                                                'Selesai' => 'bg-emerald-50 text-emerald-700',
                                                'Perlu cek chat' => 'bg-amber-50 text-amber-700',
                                                'Dibatalkan' => 'bg-rose-50 text-rose-700',
                                                'Diproses' => 'bg-sky-50 text-sky-700',
                                                default => 'bg-slate-100 text-slate-700',
                                            };
                                        @endphp
                                        <article class="table-row-card">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="eyebrow-label">{{ $transaction->reference }}</p>
                                                    <h3 class="mt-2 font-bold text-slate-900">{{ $transaction->product_name }}</h3>
                                                    <p class="mt-2 text-sm text-slate-500">{{ $transaction->buyer?->name }} → {{ $transaction->seller?->store_name ?: $transaction->seller?->name }}</p>
                                                </div>
                                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $transaction->status }}</span>
                                            </div>
                                            <div class="mt-4 flex items-center justify-between gap-4 text-sm">
                                                <span class="text-slate-500">Nominal</span>
                                                <span class="font-bold text-slate-900">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                            </div>
                                            <a class="mt-3 inline-flex text-sm font-semibold text-amber-700 transition hover:text-amber-800" href="{{ route('transactions.show', $transaction) }}">
                                                Buka detail transaksi
                                            </a>
                                        </article>
                                    @endforeach
                                </div>

                                <div class="table-surface mt-6 hidden lg:block">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-left text-sm">
                                            <thead class="table-head">
                                                <tr>
                                                    <th class="px-4 py-4 font-semibold">Invoice</th>
                                                    <th class="px-4 py-4 font-semibold">Buyer</th>
                                                    <th class="px-4 py-4 font-semibold">Seller</th>
                                                    <th class="px-4 py-4 font-semibold">Status</th>
                                                    <th class="px-4 py-4 font-semibold">Nominal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 text-slate-600">
                                                @foreach ($recentTransactions as $transaction)
                                                    @php
                                                        $statusClass = match ($transaction->status) {
                                                            'Selesai' => 'bg-emerald-50 text-emerald-700',
                                                            'Perlu cek chat' => 'bg-amber-50 text-amber-700',
                                                            'Dibatalkan' => 'bg-rose-50 text-rose-700',
                                                            'Diproses' => 'bg-sky-50 text-sky-700',
                                                            default => 'bg-slate-100 text-slate-700',
                                                        };
                                                    @endphp
                                                    <tr class="align-top">
                                                        <td class="px-4 py-4">
                                                            <a class="font-bold text-slate-900 transition hover:text-amber-700" href="{{ route('transactions.show', $transaction) }}">
                                                                {{ $transaction->reference }}
                                                            </a>
                                                            <p class="mt-1 text-xs text-slate-400">{{ $transaction->product_name }}</p>
                                                        </td>
                                                        <td class="px-4 py-4">{{ $transaction->buyer?->name }}</td>
                                                        <td class="px-4 py-4">{{ $transaction->seller?->store_name ?: $transaction->seller?->name }}</td>
                                                        <td class="px-4 py-4">
                                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $transaction->status }}</span>
                                                        </td>
                                                        <td class="px-4 py-4 font-bold text-slate-900">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="empty-state-card mt-6">Belum ada transaksi di marketplace.</div>
                            @endif
                        </article>

                        <div class="space-y-5">
                            <article class="softdash-card p-6 lg:p-7">
                                <p class="page-kicker text-amber-700">Status Breakdown</p>
                                <h2 class="section-title">Distribusi status order</h2>

                                <div class="mt-6 space-y-3">
                                    @foreach ($statusBreakdown as $item)
                                        <div class="metric-line">
                                            <span class="text-sm text-slate-600">{{ $item['label'] }}</span>
                                            <span class="text-lg font-black text-slate-900">{{ $item['value'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </article>

                            <article class="softdash-card p-6 lg:p-7">
                                <p class="page-kicker text-amber-700">Stok Kritis</p>
                                <h2 class="section-title">Produk yang perlu dipantau</h2>

                                <div class="mt-6 space-y-3">
                                    @forelse ($lowStockProducts as $product)
                                        <article class="table-row-card">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <h3 class="font-semibold text-slate-900">{{ $product->name }}</h3>
                                                    <p class="mt-2 text-sm text-slate-500">{{ $product->seller?->store_name ?: $product->seller?->name }}</p>
                                                </div>
                                                <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-700">Stok {{ $product->stock }}</span>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="empty-state-card">Belum ada listing dengan stok kritis.</div>
                                    @endforelse
                                </div>
                            </article>
                        </div>
                    </section>

                    <section class="grid gap-5 xl:grid-cols-[0.88fr,1.12fr]">
                        <article class="softdash-card p-6 lg:p-7">
                            <p class="page-kicker text-amber-700">Seller Teratas</p>
                            <h2 class="section-title">Ranking toko berdasarkan revenue</h2>

                            <div class="mt-6 space-y-3">
                                @forelse ($topSellers as $row)
                                    <article class="table-row-card">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <h3 class="font-semibold text-slate-900">{{ $row['seller']->store_name ?: $row['seller']->name }}</h3>
                                                <p class="mt-2 text-sm text-slate-500">{{ $row['active_listings'] }} listing aktif • {{ $row['orders'] }} order</p>
                                            </div>
                                            <span class="text-sm font-bold text-emerald-700">{{ \App\Support\MarketplaceUi::formatRupiah($row['revenue']) }}</span>
                                        </div>
                                    </article>
                                @empty
                                    <div class="empty-state-card">Belum ada data seller untuk diranking.</div>
                                @endforelse
                            </div>
                        </article>

                        <article class="softdash-card p-6 lg:p-7">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="page-kicker text-amber-700">Listing Aktif</p>
                                    <h2 class="section-title">Produk aktif terbaru</h2>
                                </div>
                                <span class="softdash-pill">{{ $latestProducts->count() }} listing</span>
                            </div>

                            <div class="mt-6 grid gap-4 md:grid-cols-2">
                                @forelse ($latestProducts as $product)
                                    <article class="rounded-[22px] border border-slate-200 bg-slate-50 p-5">
                                        <p class="eyebrow-label">{{ $product->game_title }}</p>
                                        <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $product->name }}</h3>

                                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                            <div>
                                                <p class="eyebrow-label">Seller</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $product->seller?->store_name ?: $product->seller?->name }}</p>
                                            </div>
                                            <div>
                                                <p class="eyebrow-label">Harga</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <p class="eyebrow-label">Stok</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $product->stock }}</p>
                                            </div>
                                        </div>

                                        <a class="primary-btn mt-5 px-4 py-3 text-sm" href="{{ route('products.show', $product) }}">
                                            Lihat listing
                                        </a>
                                    </article>
                                @empty
                                    <div class="empty-state-card md:col-span-2">Belum ada produk aktif di marketplace.</div>
                                @endforelse
                            </div>
                        </article>
                    </section>

                    <div class="flex justify-end">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="subtle-link" type="submit">Logout dari admin panel</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection
