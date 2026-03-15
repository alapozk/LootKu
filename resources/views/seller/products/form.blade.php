@php($bodyClass = 'bg-[#f4f5fb] text-slate-900')
@extends('layouts.app')

@section('content')
    <main class="page-section">
        <section class="page-hero">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="page-kicker">Seller Product Form</p>
                    <h1 class="page-title">{{ $formTitle }}</h1>
                    <p class="page-copy">
                        Form listing sekarang dibuat lebih tenang dan lebih mudah dipindai, jadi seller bisa fokus isi data produk tanpa gangguan visual berlebihan.
                    </p>
                </div>
                <a class="toolbar-pill" href="{{ route('seller.products.index') }}">Kembali ke listing</a>
            </div>
        </section>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1.05fr,0.95fr]">
            <section class="surface-card">
                <form action="{{ $formAction }}" class="grid gap-5 md:grid-cols-2" method="POST">
                    @csrf
                    @if ($formMethod !== 'POST')
                        @method($formMethod)
                    @endif

                    <div class="md:col-span-2">
                        <label class="field-label" for="name">Nama produk</label>
                        <input class="field-input" id="name" name="name" type="text" value="{{ old('name', $product->name) }}">
                        @error('name')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="game_title">Nama game</label>
                        <input class="field-input" id="game_title" name="game_title" type="text" value="{{ old('game_title', $product->game_title) }}">
                        @error('game_title')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="type">Tipe produk</label>
                        <select class="field-select" id="type" name="type">
                            @foreach ($availableTypes as $type)
                                <option value="{{ $type }}" @selected(old('type', $product->type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="price">Harga</label>
                        <input class="field-input" id="price" name="price" type="number" value="{{ old('price', $product->price) }}">
                        @error('price')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="stock">Stok</label>
                        <input class="field-input" id="stock" name="stock" type="number" value="{{ old('stock', $product->stock) }}">
                        @error('stock')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="delivery_estimate">Estimasi delivery</label>
                        <input class="field-input" id="delivery_estimate" name="delivery_estimate" type="text" value="{{ old('delivery_estimate', $product->delivery_estimate) }}">
                        @error('delivery_estimate')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="region">Region</label>
                        <input class="field-input" id="region" name="region" type="text" value="{{ old('region', $product->region) }}">
                        @error('region')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="rating">Rating awal</label>
                        <input class="field-input" id="rating" max="5" min="0" name="rating" step="0.1" type="number" value="{{ old('rating', $product->rating) }}">
                        @error('rating')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="field-label" for="tags">Tags</label>
                        <input class="field-input" id="tags" name="tags" type="text" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : '') }}" placeholder="Pisahkan dengan koma">
                        @error('tags')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="field-label" for="description">Deskripsi</label>
                        <textarea class="field-textarea" id="description" name="description" rows="5">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="md:col-span-2 flex items-center gap-3 rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        <input class="h-4 w-4 rounded border-slate-300 text-brand-700" name="is_active" type="checkbox" value="1" @checked(old('is_active', $product->is_active))>
                        Aktifkan listing setelah disimpan
                    </label>

                    <div class="md:col-span-2 flex gap-3">
                        <button class="primary-btn h-[54px] flex-1 justify-center text-base" type="submit">Simpan listing</button>
                        <a class="toolbar-pill h-[54px] flex-1 justify-center" href="{{ route('seller.products.index') }}">Batal</a>
                    </div>
                </form>
            </section>

            <aside class="surface-card-dark">
                <p class="page-kicker text-blue-200">Tips Listing</p>
                <div class="mt-6 space-y-4 text-sm text-slate-200">
                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4">Gunakan nama produk yang jelas agar mudah dicari buyer dari search bar utama.</div>
                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4">Isi game title, region, dan delivery estimate dengan spesifik supaya buyer tidak perlu bertanya ulang.</div>
                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4">Pisahkan tags dengan koma agar filter internal dan rekomendasi produk lebih mudah dikembangkan nanti.</div>
                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4">Status aktif/nonaktif bisa diubah kapan saja dari halaman kelola listing.</div>
                </div>
            </aside>
        </div>
    </main>
@endsection
