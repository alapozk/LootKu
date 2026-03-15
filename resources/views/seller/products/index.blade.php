@php($bodyClass = 'bg-[#f4f5fb] text-slate-900')
@extends('layouts.app')

@section('content')
    @php($user = auth()->user())
    <main class="page-section">
        <section class="page-hero">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="page-kicker">Seller Listing</p>
                    <h1 class="page-title">Kelola produk toko {{ $user->store_name ?: $user->name }}</h1>
                    <p class="page-copy">
                        Semua listing seller dirangkum di satu tempat yang lebih bersih, termasuk status aktif, stok, harga, dan akses cepat ke edit atau preview.
                    </p>
                </div>

                <div class="page-actions">
                    <a class="toolbar-pill" href="{{ route('seller.dashboard') }}">Kembali ke dashboard</a>
                    <a class="primary-btn px-5 py-3" href="{{ route('seller.products.create') }}">Tambah listing</a>
                </div>
            </div>
        </section>

        <section class="mt-6 surface-card">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="page-kicker">Inventory</p>
                    <h2 class="section-title">Daftar listing seller</h2>
                </div>
                <span class="toolbar-pill">{{ $products->count() }} listing</span>
            </div>

            @if ($products->count() > 0)
                <div class="mt-6 space-y-4 lg:hidden">
                    @foreach ($products as $product)
                        <article class="table-row-card">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="eyebrow-label">{{ $product->game_title }}</p>
                                    <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $product->name }}</h3>
                                    <p class="mt-2 text-sm text-slate-500">{{ $product->type }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <div class="surface-card-muted">
                                    <p class="eyebrow-label">Harga</p>
                                    <p class="mt-1 font-bold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                                <div class="surface-card-muted">
                                    <p class="eyebrow-label">Stok</p>
                                    <p class="mt-1 font-bold text-slate-900">{{ number_format($product->stock, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <a class="toolbar-pill" href="{{ route('seller.products.edit', $product) }}">Edit</a>
                                <a class="primary-btn px-4 py-3 text-sm" href="{{ route('products.show', $product) }}">Lihat</a>
                                <form action="{{ route('seller.products.toggle', $product) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="toolbar-pill" type="submit">
                                        {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="table-surface mt-6 hidden lg:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="table-head">
                                <tr>
                                    <th class="px-4 py-4 font-semibold">Produk</th>
                                    <th class="px-4 py-4 font-semibold">Tipe</th>
                                    <th class="px-4 py-4 font-semibold">Harga</th>
                                    <th class="px-4 py-4 font-semibold">Stok</th>
                                    <th class="px-4 py-4 font-semibold">Status</th>
                                    <th class="px-4 py-4 font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-slate-600">
                                @foreach ($products as $product)
                                    <tr class="align-top">
                                        <td class="px-4 py-4">
                                            <p class="font-bold text-slate-900">{{ $product->name }}</p>
                                            <p class="mt-1 text-xs uppercase tracking-[0.24em] text-slate-400">{{ $product->game_title }}</p>
                                        </td>
                                        <td class="px-4 py-4">{{ $product->type }}</td>
                                        <td class="px-4 py-4 font-semibold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-4">{{ number_format($product->stock, 0, ',', '.') }}</td>
                                        <td class="px-4 py-4">
                                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-wrap gap-2">
                                                <a class="toolbar-pill" href="{{ route('seller.products.edit', $product) }}">Edit</a>
                                                <a class="primary-btn px-4 py-3 text-sm" href="{{ route('products.show', $product) }}">Lihat</a>
                                                <form action="{{ route('seller.products.toggle', $product) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="toolbar-pill" type="submit">
                                                        {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="empty-state-card mt-6">Belum ada listing. Tambahkan produk pertama Anda dari tombol di atas.</div>
            @endif
        </section>
    </main>
@endsection
