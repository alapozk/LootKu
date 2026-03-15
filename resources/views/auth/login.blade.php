@extends('layouts.app')

@section('content')
    <main class="page-section lg:py-12">
        <section class="page-hero">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="page-kicker">Login System</p>
                    <h1 class="page-title">Masuk ke workspace buyer, seller, atau admin.</h1>
                    <p class="page-copy">
                        Email dan password sudah aktif, dan layout ini sengaja disiapkan agar nanti bisa diperluas ke Google,
                        Steam, Discord, atau WhatsApp OTP tanpa merombak keseluruhan flow akun.
                    </p>
                </div>

                <div class="page-actions">
                    <a class="toolbar-pill" href="{{ route('home') }}">Kembali ke beranda</a>
                    <a class="toolbar-pill" href="{{ route('register') }}">Daftar akun</a>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-6 xl:grid-cols-[0.92fr,1.08fr]">
            <div class="surface-card-dark">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-[18px] bg-white/10 text-lg font-black text-white">LK</span>
                    <div>
                        <p class="font-display text-2xl font-extrabold text-white">lootku</p>
                        <p class="text-sm text-slate-300">Akses buyer, seller, dan admin</p>
                    </div>
                </div>

                <div class="mt-8 space-y-3">
                    @foreach ($authMethods as $method)
                        <div class="flex items-center justify-between rounded-[22px] border border-white/10 bg-white/10 px-4 py-4">
                            <span class="font-semibold text-white">{{ $method['label'] }}</span>
                            <span class="text-sm text-slate-300">{{ $method['status'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 rounded-[26px] border border-white/10 bg-white/10 p-5 text-sm leading-7 text-slate-200">
                    <p class="font-bold text-white">Akun demo untuk testing</p>
                    <p class="mt-3">Buyer: <span class="font-bold">buyer@lootku.test</span> / <span class="font-bold">password123</span></p>
                    <p class="mt-2">Seller: <span class="font-bold">seller@lootku.test</span> / <span class="font-bold">password123</span></p>
                    <p class="mt-2">Admin: <span class="font-bold">admin@lootku.test</span> / <span class="font-bold">password123</span></p>
                    <p class="mt-3 text-slate-300">
                        Seeder hanya membuat akun untuk login. Produk dan transaksi tetap kosong sampai Anda isi sendiri saat testing.
                    </p>
                </div>
            </div>

            <section class="surface-card">
                <p class="page-kicker">Masuk</p>
                <h2 class="section-title">Masuk ke akun Anda</h2>
                <p class="section-copy">
                    Buyer diarahkan ke riwayat transaksi, seller ke dashboard toko, dan admin ke panel monitoring marketplace.
                </p>

                <form action="{{ route('login.store') }}" class="mt-8 space-y-5" method="POST">
                    @csrf
                    <div>
                        <label class="field-label" for="email">Email</label>
                        <input class="field-input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com">
                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="password">Password</label>
                        <input class="field-input" id="password" name="password" type="password" placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        <input class="h-4 w-4 rounded border-slate-300 text-brand-700" name="remember" type="checkbox" value="1">
                        Tetap masuk di perangkat ini
                    </label>

                    <button class="primary-btn h-[54px] w-full justify-center text-base" type="submit">Masuk sekarang</button>
                </form>

                <div class="mt-8 surface-card-muted text-sm text-slate-600">
                    <p class="font-bold text-slate-900">Belum punya akun?</p>
                    <p class="mt-2 leading-7">Buat akun buyer atau seller dulu, lalu riwayat transaksi akan mulai tersimpan per user.</p>
                    <a class="subtle-link mt-4 inline-flex" href="{{ route('register') }}">Daftar akun</a>
                </div>
            </section>
        </section>
    </main>
@endsection
