@extends('layouts.app')

@section('content')
    <main class="page-section lg:py-12">
        <section class="page-hero">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="page-kicker">Registrasi</p>
                    <h1 class="page-title">Satu alur daftar untuk buyer dan seller.</h1>
                    <p class="page-copy">
                        Pilih role saat membuat akun. Buyer akan fokus ke histori pembelian, sementara seller langsung masuk ke
                        workspace toko untuk mengelola listing dan transaksi.
                    </p>
                </div>

                <div class="page-actions">
                    <a class="toolbar-pill" href="{{ route('home') }}">Kembali ke beranda</a>
                    <a class="toolbar-pill" href="{{ route('login') }}">Sudah punya akun?</a>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-6 xl:grid-cols-[1.06fr,0.94fr]">
            <section class="surface-card">
                <p class="page-kicker">Buat akun</p>
                <h2 class="section-title">Mulai setup akun marketplace</h2>
                <p class="section-copy">
                    Form ini tetap sederhana, tapi strukturnya sudah siap untuk onboarding toko, verifikasi email, dan social login.
                </p>

                <form action="{{ route('register.store') }}" class="mt-8 grid gap-5 md:grid-cols-2" method="POST">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="field-label" for="name">Nama lengkap</label>
                        <input class="field-input" id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nama pengguna">
                        @error('name')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="field-label" for="email">Email</label>
                        <input class="field-input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com">
                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="role">Role akun</label>
                        <select class="field-select" id="role" name="role">
                            <option value="buyer" @selected(old('role', 'buyer') === 'buyer')>Buyer</option>
                            <option value="seller" @selected(old('role') === 'seller')>Seller</option>
                        </select>
                        @error('role')
                            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label" for="store_name">Nama toko (opsional)</label>
                        <input class="field-input" id="store_name" name="store_name" type="text" value="{{ old('store_name') }}" placeholder="Khusus seller">
                        @error('store_name')
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

                    <div>
                        <label class="field-label" for="password_confirmation">Konfirmasi password</label>
                        <input class="field-input" id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password">
                    </div>

                    <div class="md:col-span-2">
                        <button class="primary-btn h-[54px] w-full justify-center text-base" type="submit">Buat akun</button>
                    </div>
                </form>
            </section>

            <section class="surface-card-dark">
                <p class="page-kicker text-blue-200">Role Setup</p>
                <h2 class="font-display text-4xl font-extrabold leading-tight text-white">Satu form, dua workspace yang berbeda.</h2>

                <div class="mt-8 space-y-4">
                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-5">
                        <p class="text-sm font-bold uppercase tracking-[0.24em] text-blue-200">Buyer</p>
                        <p class="mt-3 text-sm leading-7 text-slate-200">
                            Buyer diarahkan ke riwayat transaksi dan nantinya bisa memantau status order, invoice, refund, dan chat.
                        </p>
                    </div>
                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-5">
                        <p class="text-sm font-bold uppercase tracking-[0.24em] text-blue-200">Seller</p>
                        <p class="mt-3 text-sm leading-7 text-slate-200">
                            Seller langsung mendapat panel untuk mengelola listing, mengecek stok, dan memproses order yang masuk.
                        </p>
                    </div>
                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-5">
                        <p class="text-sm font-bold uppercase tracking-[0.24em] text-blue-200">Siap dikembangkan</p>
                        <p class="mt-3 text-sm leading-7 text-slate-200">
                            Berikutnya tinggal sambungkan email verification, OTP, social login, dan onboarding toko yang lebih lengkap.
                        </p>
                    </div>
                </div>
            </section>
        </section>
    </main>
@endsection
