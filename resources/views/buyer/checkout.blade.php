@extends('layouts.app')

@section('content')
    @include('partials.buyer-header', ['activePage' => 'catalog'])

    <main class="page-section">
        <div class="grid gap-6 xl:grid-cols-[0.95fr,1.05fr]">
            <section class="surface-card">
                <p class="page-kicker">Checkout</p>
                <h1 class="section-title">Selesaikan pembelian Anda</h1>
                <p class="section-copy">
                    Form ini sudah membuat transaksi real ke database, mengurangi stok, dan mengirim order ke riwayat buyer serta dashboard seller.
                </p>

                <form action="{{ route('checkout.store', $productModel) }}" class="mt-8 space-y-5" method="POST">
                    @csrf
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="field-label" for="quantity">Jumlah</label>
                            <input class="field-input" id="quantity" max="{{ $productModel->stock }}" min="1" name="quantity" type="number" value="{{ old('quantity', 1) }}">
                            @error('quantity')
                                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="field-label" for="payment_method">Metode pembayaran</label>
                            <select class="field-select" id="payment_method" name="payment_method">
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method }}" @selected(old('payment_method', 'Saldo') === $method)>{{ $method }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="field-label" for="game_user_id">Game User ID / UID / Username</label>
                        <input class="field-input" id="game_user_id" name="game_user_id" type="text" value="{{ old('game_user_id') }}" placeholder="Contoh: UID-123456 atau username game">
                        @error('game_user_id')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="buyer_note">Catatan untuk seller</label>
                        <textarea class="field-textarea" id="buyer_note" name="buyer_note" rows="4" placeholder="Tambahkan instruksi khusus kalau perlu">{{ old('buyer_note') }}</textarea>
                        @error('buyer_note')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button class="primary-btn h-[54px] w-full justify-center text-base" type="submit">Buat transaksi sekarang</button>
                </form>
            </section>

            <aside class="surface-card-dark">
                <p class="page-kicker text-blue-200">Ringkasan Produk</p>
                <div class="mt-5 rounded-[30px] bg-gradient-to-br {{ $product['tone'] }} p-6">
                    <p class="eyebrow-label text-white/70">{{ $product['game'] }}</p>
                    <h2 class="mt-3 text-3xl font-extrabold">{{ $product['name'] }}</h2>
                    <p class="mt-3 text-sm text-white/80">{{ $product['description'] }}</p>
                </div>

                <div class="mt-6 space-y-4 rounded-[28px] border border-white/10 bg-white/10 p-5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-300">Seller</span>
                        <span class="font-bold text-white">{{ $product['seller'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-300">Harga satuan</span>
                        <span class="font-bold text-white">{{ $product['price'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-300">Stok tersedia</span>
                        <span class="font-bold text-white">{{ $product['stock'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-300">Estimasi delivery</span>
                        <span class="font-bold text-white">{{ $product['delivery'] }}</span>
                    </div>
                </div>

                <div class="mt-6 rounded-[28px] border border-white/10 bg-white/10 p-5 text-sm leading-7 text-slate-200">
                    <p>Metode `Saldo` akan langsung membuat order masuk status `Diproses`.</p>
                    <p class="mt-3">Metode lain akan masuk status `Menunggu Pembayaran` agar nanti bisa disambungkan ke gateway pembayaran.</p>
                </div>
            </aside>
        </div>
    </main>

    @include('partials.site-footer')
@endsection
