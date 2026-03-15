@php($user = auth()->user())

<header class="bg-brand-700 text-white shadow-[0_24px_60px_-32px_rgba(15,34,90,0.75)]">
    <div class="border-b border-white/10 bg-brand-950/20">
        <div class="shell flex flex-wrap items-center justify-between gap-3 py-3 text-sm text-blue-100">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                    <span class="text-[10px] font-black tracking-[0.24em]">NEW</span>
                    <span>Mode jual item game siap dikembangkan ke escrow</span>
                </span>
                <span class="hidden text-white/70 md:inline">Bantuan</span>
                <span class="hidden text-white/70 md:inline">ID - IDR</span>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <span class="hidden rounded-full bg-white/10 px-3 py-2 text-xs font-bold uppercase tracking-[0.24em] md:inline-flex">
                        {{ $user->role }}
                    </span>
                    @if (! $user->isAdmin())
                        <a class="text-white/80 transition hover:text-white" href="{{ route('transactions.index') }}">
                            Riwayat
                        </a>
                    @endif
                    @if ($user->isSeller())
                        <a class="text-white/80 transition hover:text-white" href="{{ route('seller.dashboard') }}">
                            Dashboard Penjual
                        </a>
                    @elseif ($user->isAdmin())
                        <a class="text-white/80 transition hover:text-white" href="{{ route('admin.dashboard') }}">
                            Dashboard Admin
                        </a>
                    @else
                        <a class="text-white/80 transition hover:text-white" href="{{ route('transactions.index') }}">
                            Riwayat
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="rounded-full bg-white px-4 py-2 text-sm font-bold text-brand-700 transition hover:bg-blue-50" type="submit">
                            Logout
                        </button>
                    </form>
                @else
                    <a class="text-white/80 transition hover:text-white" href="{{ route('login') }}">
                        Masuk
                    </a>
                    <a class="rounded-full bg-white px-4 py-2 text-sm font-bold text-brand-700 transition hover:bg-blue-50" href="{{ route('register') }}">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="shell py-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-center">
            <a class="flex items-center gap-3" href="{{ route('home') }}">
                <span class="flex h-12 w-12 items-center justify-center rounded-[18px] bg-white/10 text-lg font-black shadow-inner shadow-white/20">
                    LK
                </span>
                <div>
                    <p class="font-display text-3xl font-extrabold tracking-tight">lootku</p>
                    <p class="text-sm text-blue-100/80">Marketplace item game</p>
                </div>
            </a>

            <div class="flex-1">
                <form action="{{ route('catalog') }}" class="flex flex-col gap-3 lg:flex-row" method="GET">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-blue-900/50">Q</span>
                        <input
                            class="h-14 w-full rounded-[22px] border border-white/30 bg-white px-12 text-slate-900 outline-none ring-0 placeholder:text-slate-400 focus:border-brand-200"
                            name="q"
                            placeholder="Cari game, diamond, hero, voucher, item langka..."
                            type="text"
                            value="{{ request('q') }}"
                        >
                    </div>
                    <button class="primary-btn h-14 px-6" type="submit">Cari</button>
                </form>

                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($topTags as $tag)
                        <a
                            class="rounded-full bg-white/20 px-3 py-2 text-sm text-white/90 transition hover:bg-white/30"
                            href="{{ route('catalog', ['q' => $tag]) }}"
                        >
                            {{ $tag }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <nav class="bg-brand-950/30 backdrop-blur">
        <div class="shell flex gap-2 overflow-x-auto py-4 text-sm font-semibold">
            @foreach ($navigation as $link)
                @php($isActive = ($activePage ?? 'home') === $link['key'])
                <a
                    class="whitespace-nowrap rounded-full px-4 py-2 transition {{ $isActive ? 'bg-white text-brand-700' : 'bg-white/10 text-white hover:bg-white/20' }}"
                    href="{{ $link['href'] }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </nav>
</header>
