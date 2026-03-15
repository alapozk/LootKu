<footer class="mt-20 pb-8">
    <div class="shell">
        <div class="surface-card-dark overflow-hidden">
            <div class="grid gap-8 lg:grid-cols-[1.25fr,0.8fr,0.95fr]">
                <div class="space-y-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-[18px] bg-white/10 font-black text-white">LK</span>
                        <div>
                            <p class="font-display text-2xl font-extrabold text-white">lootku</p>
                            <p class="text-sm text-slate-300">Marketplace item game berbasis Laravel.</p>
                        </div>
                    </div>

                    <p class="max-w-xl text-sm leading-7 text-slate-300">
                        Buyer storefront, autentikasi multi-role, transaksi, chat order, dan dashboard seller/admin sudah tersusun
                        dalam satu alur yang siap dilanjutkan ke payment gateway dan otomasi operasional.
                    </p>

                    <div class="flex flex-wrap gap-2 text-xs font-semibold text-slate-200">
                        <span class="rounded-full border border-white/10 bg-white/10 px-3 py-2">Buyer</span>
                        <span class="rounded-full border border-white/10 bg-white/10 px-3 py-2">Seller</span>
                        <span class="rounded-full border border-white/10 bg-white/10 px-3 py-2">Admin</span>
                        <span class="rounded-full border border-white/10 bg-white/10 px-3 py-2">Riwayat Transaksi</span>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-slate-400">Navigasi</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <a class="block transition hover:text-white" href="{{ route('home') }}">Beranda buyer</a>
                        <a class="block transition hover:text-white" href="{{ route('catalog') }}">Katalog item</a>
                        <a class="block transition hover:text-white" href="{{ route('transactions.index') }}">Riwayat transaksi</a>
                        <a class="block transition hover:text-white" href="{{ route('seller.dashboard') }}">Dashboard penjual</a>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-slate-400">Arah Pengembangan</p>
                    <div class="mt-4 space-y-4 text-sm text-slate-300">
                        <div class="rounded-[22px] border border-white/10 bg-white/10 px-4 py-4">
                            Payment gateway real-time dan status pembayaran otomatis.
                        </div>
                        <div class="rounded-[22px] border border-white/10 bg-white/10 px-4 py-4">
                            Notifikasi order, payout seller, dan approval operasional.
                        </div>
                        <div class="rounded-[22px] border border-white/10 bg-white/10 px-4 py-4">
                            Moderasi admin, dispute handling, dan analitik marketplace lebih lengkap.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
