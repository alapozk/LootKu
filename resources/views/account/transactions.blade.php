@extends('layouts.app')

@php($bodyClass = 'bg-slate-100 text-slate-900')

@section('content')
    @php($user = auth()->user())
    <main class="page-section lg:py-12">
        <section class="page-hero">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="page-kicker">Akun</p>
                    <h1 class="page-title">Riwayat transaksi {{ $user->name }}</h1>
                    <p class="page-copy">
                        Semua pembelian buyer dan order yang diterima seller dirangkum di sini, lengkap dengan status, nominal, dan akses cepat ke detail transaksi.
                    </p>
                </div>

                <div class="page-actions">
                    <a class="toolbar-pill" href="{{ route('home') }}">Buyer storefront</a>
                    @if ($user->isSeller())
                        <a class="toolbar-pill" href="{{ route('seller.dashboard') }}">Dashboard penjual</a>
                    @endif
                    @if ($user->isAdmin())
                        <a class="toolbar-pill" href="{{ route('admin.dashboard') }}">Dashboard admin</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="toolbar-pill" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="stat-card-light">
                <p class="stat-label">Total Pembelian</p>
                <p class="mt-4 text-3xl font-black text-slate-900">{{ $purchaseSummary['count'] }}</p>
                <p class="mt-2 text-sm text-slate-500">order buyer tercatat</p>
            </article>
            <article class="stat-card-light">
                <p class="stat-label">Pembelian Aktif</p>
                <p class="mt-4 text-3xl font-black text-slate-900">{{ $purchaseSummary['active_count'] }}</p>
                <p class="mt-2 text-sm text-slate-500">menunggu update atau pembayaran</p>
            </article>
            <article class="stat-card-light">
                <p class="stat-label">Total Belanja</p>
                <p class="mt-4 text-3xl font-black text-slate-900">Rp {{ number_format($purchaseSummary['gross_total'], 0, ',', '.') }}</p>
                <p class="mt-2 text-sm text-slate-500">transaksi dengan status dibayar</p>
            </article>
            <article class="stat-card-light">
                <p class="stat-label">Penjualan Selesai</p>
                <p class="mt-4 text-3xl font-black text-slate-900">{{ $salesSummary['completed_count'] }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $user->isSeller() ? 'untuk akun seller ini' : '0 jika bukan seller' }}</p>
            </article>
        </section>

        <section class="mt-6 grid gap-6 xl:grid-cols-[1.08fr,0.92fr]">
            <div class="surface-card">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="page-kicker">Pembelian</p>
                        <h2 class="section-title">Riwayat order buyer</h2>
                    </div>
                    <span class="toolbar-pill">{{ $purchases->count() }} transaksi</span>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($purchases as $transaction)
                        <article class="table-row-card">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <p class="eyebrow-label">{{ $transaction->reference }}</p>
                                    <h3 class="mt-2 text-xl font-extrabold text-slate-900">
                                        @if ($transaction->product)
                                            <a class="transition hover:text-brand-700" href="{{ route('products.show', $transaction->product) }}">{{ $transaction->product_name }}</a>
                                        @else
                                            {{ $transaction->product_name }}
                                        @endif
                                    </h3>
                                    <p class="mt-2 text-sm text-slate-600">{{ $transaction->game_title }} • {{ $transaction->product_type }} • Qty {{ $transaction->quantity }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Seller: {{ $transaction->seller?->store_name ?: $transaction->seller?->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Game ID: {{ $transaction->game_user_id ?: '-' }}</p>
                                </div>
                                <div class="text-left lg:text-right">
                                    <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700">{{ $transaction->status }}</span>
                                    <p class="mt-3 text-2xl font-black text-brand-700">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $transaction->ordered_at?->format('d M Y H:i') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Pembayaran: {{ $transaction->payment_method }}</p>
                                </div>
                            </div>

                            @if ($transaction->buyer_note)
                                <div class="mt-4 surface-card-muted text-sm text-slate-600">
                                    Catatan buyer: {{ $transaction->buyer_note }}
                                </div>
                            @endif

                            <div class="mt-4 flex flex-wrap gap-3">
                                <a class="primary-btn px-4 py-3 text-sm" href="{{ route('transactions.show', $transaction) }}">
                                    Lihat detail transaksi
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state-card">Belum ada transaksi pembelian untuk akun ini.</div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="surface-card">
                    <p class="page-kicker">Status Tracker</p>
                    <h2 class="section-title">Ringkasan status transaksi</h2>

                    <div class="mt-6 space-y-3">
                        <div class="surface-card-muted text-sm text-slate-600">Menunggu Pembayaran: transaksi dibuat tapi dana belum masuk.</div>
                        <div class="surface-card-muted text-sm text-slate-600">Diproses: seller sedang menyelesaikan order.</div>
                        <div class="surface-card-muted text-sm text-slate-600">Perlu cek chat: seller butuh konfirmasi tambahan dari buyer.</div>
                        <div class="surface-card-muted text-sm text-slate-600">Selesai: order delivered dan histori final sudah tercatat.</div>
                        <div class="surface-card-muted text-sm text-slate-600">Dibatalkan: order dihentikan dan stok dikembalikan ke listing.</div>
                    </div>
                </div>

                @if ($user->isSeller())
                    <div class="surface-card-dark">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="page-kicker text-blue-200">Penjualan</p>
                                <h2 class="mt-2 text-2xl font-extrabold text-white">Riwayat order yang masuk</h2>
                            </div>
                            <span class="rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-bold text-slate-200">{{ $sales->count() }} transaksi</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse ($sales as $transaction)
                                <article class="rounded-[24px] border border-white/10 bg-white/10 p-4">
                                    <div class="flex flex-col gap-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="eyebrow-label text-blue-200">{{ $transaction->reference }}</p>
                                                <p class="mt-2 font-bold text-white">{{ $transaction->product_name }}</p>
                                                <p class="mt-1 text-sm text-slate-300">Buyer: {{ $transaction->buyer?->name }}</p>
                                                <p class="mt-1 text-sm text-slate-300">Game ID: {{ $transaction->game_user_id ?: '-' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white">{{ $transaction->status }}</span>
                                                <p class="mt-3 font-black text-white">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                                            </div>
                                        </div>

                                        <form action="{{ route('seller.transactions.update-status', $transaction) }}" class="flex flex-col gap-3 sm:flex-row" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select class="rounded-[16px] border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-white" name="status">
                                                @foreach ($availableStatuses as $status)
                                                    <option value="{{ $status }}" @selected($transaction->status === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                            <button class="rounded-[16px] bg-white/10 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/20" type="submit">
                                                Update status
                                            </button>
                                        </form>

                                        <a class="rounded-[16px] bg-brand-700 px-5 py-3 text-center text-sm font-bold text-white transition hover:bg-brand-600" href="{{ route('transactions.show', $transaction) }}">
                                            Detail transaksi
                                        </a>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-[24px] border border-white/10 bg-white/10 p-5 text-sm text-slate-200">Belum ada penjualan untuk akun seller ini.</div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>
@endsection
