@php
    $bodyClass = 'bg-[#f4f5fb] text-slate-900';
@endphp
@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $storeName = $storeOverview['name'];
        $menuGroups = [
            [
                'title' => 'Operasional',
                'links' => [
                    ['label' => 'Dashboard', 'tab' => 'overview', 'active' => true],
                    ['label' => 'Listing', 'tab' => 'listing', 'active' => false],
                    ['label' => 'Transaksi', 'tab' => 'transactions', 'active' => false],
                    ['label' => 'Storefront', 'tab' => 'storefront', 'active' => false],
                ],
            ],
            [
                'title' => 'Toko',
                'links' => [
                    ['label' => 'Tambah produk', 'tab' => 'listing', 'active' => false],
                    ['label' => 'Order aktif', 'tab' => 'transactions', 'active' => false],
                ],
            ],
        ];

        $orderTotal = max(1, collect($orderBreakdown)->sum('value'));
        $donutColors = ['#7c3aed', '#4f46e5', '#38bdf8', '#22c55e', '#f59e0b'];
        $running = 0;
        $segments = [];
        foreach ($orderBreakdown as $index => $item) {
            $percent = ($item['value'] / $orderTotal) * 100;
            $start = $running;
            $running += $percent;
            $segments[] = ($donutColors[$index % count($donutColors)]).' '.$start.'% '.$running.'%';
        }
        $donutGradient = implode(', ', $segments);

        $statusMap = collect($orderBreakdown)
            ->mapWithKeys(fn (array $item) => [$item['label'] => $item['value']]);

        $transactionHighlights = [
            ['label' => 'Menunggu Pembayaran', 'value' => $statusMap->get('Menunggu Pembayaran', 0), 'note' => 'Order yang masih menunggu pembayaran buyer.'],
            ['label' => 'Diproses', 'value' => $statusMap->get('Diproses', 0), 'note' => 'Order yang perlu Anda lanjutkan sekarang.'],
            ['label' => 'Perlu cek chat', 'value' => $statusMap->get('Perlu cek chat', 0), 'note' => 'Buyer menunggu klarifikasi atau balasan.'],
            ['label' => 'Selesai', 'value' => $statusMap->get('Selesai', 0), 'note' => 'Order yang sudah selesai dengan baik.'],
        ];

        $inventoryCollection = collect($inventory);
        $activeInventoryCount = $inventoryCollection->where('is_active', true)->count();
        $inactiveInventoryCount = $inventoryCollection->where('is_active', false)->count();
        $totalSoldCount = $inventoryCollection->sum('sold_count');
        $averageRating = $inventoryCollection->count() > 0
            ? number_format((float) $inventoryCollection->avg(fn (array $product) => (float) ($product['rating'] ?? 0)), 1)
            : '0.0';

        $listingHighlights = [
            ['label' => 'Listing aktif', 'value' => $activeInventoryCount, 'note' => 'Produk yang sedang tayang di katalog buyer.'],
            ['label' => 'Listing nonaktif', 'value' => $inactiveInventoryCount, 'note' => 'Produk yang belum ditampilkan ke buyer.'],
            ['label' => 'Stok kritis', 'value' => collect($lowStockProducts)->count(), 'note' => 'Listing yang perlu diisi ulang.'],
            ['label' => 'Total terjual', 'value' => number_format($totalSoldCount, 0, ',', '.'), 'note' => 'Akumulasi produk terjual dari listing Anda.'],
        ];

        $storefrontHighlights = [
            ['label' => 'Listing live', 'value' => $storeOverview['active_listings'], 'note' => 'Produk yang siap muncul di buyer storefront.'],
            ['label' => 'Produk terjual', 'value' => number_format($totalSoldCount, 0, ',', '.'), 'note' => 'Akumulasi transaksi dari listing Anda.'],
            ['label' => 'Stok kritis', 'value' => collect($lowStockProducts)->count(), 'note' => 'Listing yang paling butuh perhatian.'],
            ['label' => 'Rating rata-rata', 'value' => $averageRating, 'note' => 'Rata-rata rating produk aktif saat ini.'],
        ];

        $featuredInventory = $inventoryCollection->take(4)->all();
        $recentOrderPreview = collect($recentOrders)->take(3);
        $statusGuides = [
            'Menunggu Pembayaran: tunggu dana buyer masuk atau follow up bila perlu.',
            'Diproses: prioritaskan order dengan data lengkap agar penyelesaian cepat.',
            'Perlu cek chat: balas buyer dari halaman detail transaksi agar order tidak macet.',
            'Selesai: jadikan referensi untuk listing yang paling laku dan cepat selesai.',
        ];
        $storefrontGuides = [
            'Judul listing yang jelas membantu buyer menemukan produk lewat search.',
            'Stok dan estimasi delivery harus realistis supaya buyer tidak kecewa.',
            'Gunakan kombinasi type, region, dan tags agar listing lebih mudah dipilah.',
        ];
    @endphp

    <div class="softdash-shell min-h-screen" data-workspace data-workspace-default="overview">
        <div class="mx-auto flex max-w-[1500px] gap-4 px-4 py-5 sm:px-5 lg:px-6 xl:px-8">
            <aside class="softdash-sidebar hidden lg:flex lg:flex-col">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[20px] bg-emerald-50 text-base font-black text-emerald-700 shadow-sm ring-1 ring-emerald-100">
                            LK
                        </div>
                        <div>
                            <p class="font-display text-2xl font-black tracking-tight text-emerald-950">Lootku</p>
                            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-slate-400">Seller workspace</p>
                        </div>
                    </div>
                    <span class="rounded-full border border-slate-100 bg-white px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500 shadow-sm">{{ $user->role }}</span>
                </div>

                <div class="mt-8 space-y-8">
                    @foreach ($menuGroups as $group)
                        <div>
                            <p class="softdash-label">{{ $group['title'] }}</p>
                            <div class="mt-3 space-y-2">
                                @foreach ($group['links'] as $link)
                                    @php
                                        $activeStyle = $group['title'] === 'Operasional' ? 'softdash-nav-link-active-blue' : 'softdash-nav-link-active-emerald';
                                    @endphp
                                    <button
                                        class="softdash-nav-link w-full text-left {{ $link['active'] ? $activeStyle : '' }}"
                                        data-workspace-label="{{ $link['label'] }}"
                                        data-workspace-trigger="{{ $link['tab'] }}"
                                        type="button"
                                    >
                                        <span>{{ $link['label'] }}</span>
                                        <span class="rounded-full bg-white/60 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider shadow-sm" data-workspace-badge @if (! $link['active']) hidden @endif>aktif</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-auto rounded-[28px] border border-slate-100 bg-white p-5 shadow-[0_20px_40px_-30px_rgba(15,23,42,0.15)]">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-sm font-black text-emerald-700 ring-4 ring-emerald-50/50">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate font-display text-[15px] font-bold text-slate-900">{{ $storeName }}</p>
                            <p class="truncate text-xs font-medium text-slate-400">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Bergabung</p>
                            <p class="mt-1 font-display text-[13px] font-extrabold text-slate-900">{{ $storeOverview['joined_at'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Order</p>
                            <p class="mt-1 font-display text-[13px] font-extrabold text-slate-900">{{ $storeOverview['total_orders'] }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="min-w-0 flex-1">
                <div class="space-y-5">
                    <div class="flex items-center justify-between lg:hidden">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Seller Workspace</p>
                            <h1 class="mt-2 font-display text-3xl font-extrabold text-slate-900" data-workspace-current>Dashboard</h1>
                        </div>
                        <button class="softdash-pill" data-workspace-label="Listing" data-workspace-trigger="listing" type="button">
                            Tambah produk
                        </button>
                    </div>

                    <div class="softdash-mobile-nav">
                        <button class="softdash-mobile-link softdash-mobile-link-active" data-workspace-label="Dashboard" data-workspace-trigger="overview" type="button">
                            Dashboard
                        </button>
                        <button class="softdash-mobile-link" data-workspace-label="Listing" data-workspace-trigger="listing" type="button">
                            Listing
                        </button>
                        <button class="softdash-mobile-link" data-workspace-label="Transaksi" data-workspace-trigger="transactions" type="button">
                            Transaksi
                        </button>
                        <button class="softdash-mobile-link" data-workspace-label="Storefront" data-workspace-trigger="storefront" type="button">
                            Storefront
                        </button>
                    </div>

                    <div class="hidden items-start justify-between gap-4 lg:flex">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700" data-workspace-kicker>Dashboard</p>
                            <h1 class="mt-2 font-display text-5xl font-extrabold tracking-tight text-slate-900" data-workspace-title>Seller analytics</h1>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-500" data-workspace-copy>
                                Workspace toko untuk memantau omzet, kesehatan listing, dan order yang perlu ditindak.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="softdash-pill">Time period: 30 hari</span>
                            <a class="softdash-pill" href="{{ route('home') }}">Buyer storefront</a>
                            <a class="primary-btn px-5 py-3" href="{{ route('seller.products.create') }}">Buat listing baru</a>
                        </div>
                    </div>

                    <section
                        class="space-y-5"
                        data-panel-copy="Workspace toko untuk memantau omzet, kesehatan listing, dan order yang perlu ditindak."
                        data-panel-kicker="Dashboard"
                        data-panel-title="Seller analytics"
                        data-workspace-panel="overview"
                    >
                        <section class="grid gap-4 xl:grid-cols-[repeat(4,minmax(0,1fr)),180px]">
                            @foreach ($dashboardStats as $stat)
                                <article class="softdash-stat">
                                    <p class="text-sm text-slate-400">{{ $stat['label'] }}</p>
                                    <div class="mt-4 flex items-end justify-between gap-4">
                                        <p class="text-3xl font-black tracking-tight text-slate-900">{{ $stat['value'] }}</p>
                                        <span class="text-sm font-semibold text-emerald-600">{{ $stat['delta'] }}</span>
                                    </div>
                                </article>
                            @endforeach

                            <button
                                class="softdash-stat flex flex-col items-center justify-center border-dashed text-center transition hover:border-emerald-200 hover:bg-emerald-50/50"
                                data-workspace-label="Listing"
                                data-workspace-trigger="listing"
                                type="button"
                            >
                                <div class="flex h-11 w-11 items-center justify-center rounded-[16px] bg-slate-100 text-lg font-bold text-slate-500">+</div>
                                <p class="mt-3 font-semibold text-slate-700">Buka listing</p>
                            </button>
                        </section>

                        <section class="grid gap-5 2xl:grid-cols-[1.5fr,0.95fr]">
                            <article class="softdash-card p-6 lg:p-7">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Product sales</h2>
                                        <p class="mt-2 text-sm text-slate-500">Ringkasan performa penjualan 7 hari terakhir.</p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-4 text-sm">
                                        <span class="inline-flex items-center gap-2 text-slate-600">
                                            <span class="h-3 w-3 rounded-full bg-[#4d7cfe]"></span>
                                            Gross margin
                                        </span>
                                        <span class="inline-flex items-center gap-2 text-slate-600">
                                            <span class="h-3 w-3 rounded-full bg-[#f7a33a]"></span>
                                            Revenue
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-8 grid grid-cols-7 gap-4">
                                    @foreach ($weeklySales as $bar)
                                        @php
                                            $secondaryHeight = max(12, (int) round($bar['height'] * 0.68));
                                        @endphp
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="flex h-56 items-end gap-2">
                                                <div class="w-4 rounded-t-full bg-[#4d7cfe]" style="height: {{ $secondaryHeight }}%;"></div>
                                                <div class="w-4 rounded-t-full bg-[#f7a33a]" style="height: {{ $bar['height'] }}%;"></div>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-sm font-semibold text-slate-700">{{ $bar['day'] }}</p>
                                                <p class="mt-1 text-xs text-slate-400">{{ $bar['value'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </article>

                            <div class="space-y-5">
                                <article class="softdash-card p-6 lg:p-7">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Fokus toko</h2>
                                            <p class="mt-2 text-sm text-slate-500">Yang perlu dicek sekarang.</p>
                                        </div>
                                        <span class="softdash-pill">{{ collect($actionItems)->sum('value') }} item</span>
                                    </div>

                                    <div class="mt-6 space-y-3">
                                        @foreach ($actionItems as $item)
                                            <div class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <p class="font-semibold text-slate-800">{{ $item['label'] }}</p>
                                                    <span class="text-lg font-black text-slate-900">{{ $item['value'] }}</span>
                                                </div>
                                                <p class="mt-2 text-sm text-slate-500">{{ $item['note'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </article>

                                <article class="softdash-card p-6 lg:p-7" x-data="{ openWithdraw: false }">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Wallet</h2>
                                            <p class="mt-2 text-sm text-slate-500">Pergerakan saldo terbaru.</p>
                                        </div>
                                        <button @click="openWithdraw = true" class="rounded-[16px] bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-200" type="button">
                                            Tarik Saldo
                                        </button>
                                    </div>

                                    <!-- Withdraw Modal -->
                                    <template x-teleport="body">
                                        <div x-show="openWithdraw" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm" style="display: none;">
                                            <div @click.outside="openWithdraw = false" class="w-full max-w-md rounded-[24px] bg-white p-6 shadow-xl">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-xl font-extrabold text-slate-900">Tarik Saldo</h3>
                                                    <button @click="openWithdraw = false" class="text-slate-400 hover:text-slate-600">✕</button>
                                                </div>
                                                <p class="mt-2 pl-1 pr-1 text-sm text-slate-500">Saldo saat ini: <span class="font-bold text-emerald-600">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span></p>

                                                <form action="{{ route('seller.withdrawals.store') }}" method="POST" class="mt-6 space-y-4">
                                                    @csrf
                                                    <div>
                                                        <label class="field-label" for="amount">Nominal Penarikan</label>
                                                        <input type="number" name="amount" id="amount" class="field-input" min="10000" max="{{ auth()->user()->balance }}" required placeholder="Minimal 10000">
                                                    </div>
                                                    <div>
                                                        <label class="field-label" for="bank_name">Nama Bank / E-Wallet</label>
                                                        <select name="bank_name" id="bank_name" class="field-select" required>
                                                            <option value="BCA">BCA</option>
                                                            <option value="Mandiri">Mandiri</option>
                                                            <option value="BNI">BNI</option>
                                                            <option value="BRI">BRI</option>
                                                            <option value="DANA">DANA</option>
                                                            <option value="OVO">OVO</option>
                                                            <option value="Gopay">Gopay</option>
                                                            <option value="ShopeePay">ShopeePay</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="field-label" for="bank_account">Nomor Rekening / HP</label>
                                                        <input type="text" name="bank_account" id="bank_account" class="field-input" required>
                                                    </div>
                                                    <button type="submit" class="primary-btn mt-4 w-full justify-center">Ajukan Penarikan</button>
                                                </form>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="mt-6 space-y-3">
                                        @forelse ($walletEntries as $entry)
                                            <div class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <p class="font-semibold text-slate-800">{{ $entry['title'] }}</p>
                                                        <p class="mt-2 text-sm text-slate-500">{{ $entry['time'] }}</p>
                                                    </div>
                                                    <span class="text-sm font-bold text-emerald-600">{{ $entry['value'] }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-[20px] border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-sm text-slate-500">
                                                Belum ada pergerakan saldo.
                                            </div>
                                        @endforelse
                                    </div>
                                </article>
                            </div>
                        </section>

                        <section class="grid gap-5 2xl:grid-cols-[1.08fr,1.12fr]">
                            <article class="softdash-card p-6 lg:p-7">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div>
                                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Sales by order status</h2>
                                        <p class="mt-2 text-sm text-slate-500">Distribusi order berdasarkan status transaksi.</p>
                                    </div>
                                </div>

                                <div class="mt-8 grid gap-6 lg:grid-cols-[1fr,240px] lg:items-center">
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        @foreach ($orderBreakdown as $index => $item)
                                            <div class="rounded-[18px] border border-slate-200 bg-slate-50 px-4 py-4">
                                                <div class="flex items-center gap-3">
                                                    <span class="h-3 w-3 rounded-full" style="background-color: {{ $donutColors[$index % count($donutColors)] }}"></span>
                                                    <p class="font-semibold text-slate-800">{{ $item['label'] }}</p>
                                                </div>
                                                <p class="mt-3 text-sm text-slate-500">
                                                    {{ number_format(($item['value'] / $orderTotal) * 100, 0, ',', '.') }}% dari total
                                                </p>
                                                <p class="mt-1 text-lg font-black text-slate-900">{{ $item['value'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mx-auto flex h-[220px] w-[220px] items-center justify-center rounded-full" style="background: conic-gradient({{ $donutGradient }});">
                                        <div class="flex h-[116px] w-[116px] flex-col items-center justify-center rounded-full bg-white text-center shadow-inner">
                                            <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Total order</p>
                                            <p class="mt-2 text-3xl font-black text-slate-900">{{ collect($orderBreakdown)->sum('value') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </article>

                            <article class="softdash-card p-6 lg:p-7">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Quick access</h2>
                                        <p class="mt-2 text-sm text-slate-500">Pindah panel tanpa keluar dari dashboard ini.</p>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-3">
                                    <button
                                        class="w-full rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4 text-left transition hover:border-emerald-200 hover:bg-emerald-50/70"
                                        data-workspace-label="Transaksi"
                                        data-workspace-trigger="transactions"
                                        type="button"
                                    >
                                        <p class="font-semibold text-slate-900">Buka panel transaksi</p>
                                        <p class="mt-2 text-sm text-slate-500">Lihat order terbaru, update status, dan cek prioritas seller.</p>
                                    </button>
                                    <button
                                        class="w-full rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4 text-left transition hover:border-emerald-200 hover:bg-emerald-50/70"
                                        data-workspace-label="Listing"
                                        data-workspace-trigger="listing"
                                        type="button"
                                    >
                                        <p class="font-semibold text-slate-900">Buka panel listing</p>
                                        <p class="mt-2 text-sm text-slate-500">Kelola produk aktif, cek stok kritis, dan edit listing.</p>
                                    </button>
                                    <button
                                        class="w-full rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4 text-left transition hover:border-emerald-200 hover:bg-emerald-50/70"
                                        data-workspace-label="Storefront"
                                        data-workspace-trigger="storefront"
                                        type="button"
                                    >
                                        <p class="font-semibold text-slate-900">Buka panel storefront</p>
                                        <p class="mt-2 text-sm text-slate-500">Preview bagaimana toko dan listing Anda terlihat di sisi buyer.</p>
                                    </button>
                                </div>

                                <div class="mt-6 space-y-3">
                                    @forelse ($recentOrderPreview as $order)
                                        <div class="rounded-[20px] border border-slate-200 bg-white px-4 py-4">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">{{ $order->reference }}</p>
                                                    <p class="mt-2 font-semibold text-slate-900">{{ $order->product_name }}</p>
                                                </div>
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $order->status }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-[20px] border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-sm text-slate-500">
                                            Belum ada order terbaru untuk ditampilkan.
                                        </div>
                                    @endforelse
                                </div>
                            </article>
                        </section>
                    </section>

                    <section
                        class="space-y-5"
                        data-panel-copy="Pantau order masuk, update status seller, dan fokuskan perhatian ke transaksi yang butuh aksi."
                        data-panel-kicker="Transaksi"
                        data-panel-title="Order management"
                        data-workspace-panel="transactions"
                        hidden
                    >
                        <section class="grid gap-4 xl:grid-cols-4">
                            @foreach ($transactionHighlights as $stat)
                                <article class="softdash-stat">
                                    <p class="text-sm text-slate-400">{{ $stat['label'] }}</p>
                                    <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $stat['value'] }}</p>
                                    <p class="mt-3 text-sm leading-6 text-slate-500">{{ $stat['note'] }}</p>
                                </article>
                            @endforeach
                        </section>

                        <section class="grid gap-5 2xl:grid-cols-[1.14fr,0.86fr]">
                            <article class="softdash-card p-6 lg:p-7">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Recent orders</h2>
                                        <p class="mt-2 text-sm text-slate-500">Order terbaru yang perlu Anda pantau atau update.</p>
                                    </div>
                                    <span class="softdash-pill">{{ $recentOrders->count() }} transaksi</span>
                                </div>

                                @if ($recentOrders->count() > 0)
                                    <div class="mt-6 space-y-3 lg:hidden">
                                        @foreach ($recentOrders as $order)
                                            @php
                                                $statusClass = match ($order->status) {
                                                    'Selesai' => 'bg-emerald-50 text-emerald-700',
                                                    'Perlu cek chat' => 'bg-amber-50 text-amber-700',
                                                    'Dibatalkan' => 'bg-rose-50 text-rose-700',
                                                    'Diproses' => 'bg-sky-50 text-sky-700',
                                                    default => 'bg-slate-100 text-slate-700',
                                                };
                                            @endphp
                                            <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-4">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">{{ $order->reference }}</p>
                                                        <h3 class="mt-2 font-bold text-slate-900">{{ $order->product_name }}</h3>
                                                        <p class="mt-2 text-sm text-slate-500">{{ $order->buyer?->name }} • {{ $order->ordered_at?->format('d M Y H:i') }}</p>
                                                    </div>
                                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $order->status }}</span>
                                                </div>

                                                <div class="mt-4 flex items-center justify-between text-sm">
                                                    <span class="text-slate-500">Nominal</span>
                                                    <span class="font-bold text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                                </div>

                                                <form action="{{ route('seller.transactions.update-status', $order) }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr,auto]" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select class="h-11 rounded-[16px] border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none" name="status">
                                                        @foreach ($availableStatuses as $status)
                                                            <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="rounded-[16px] bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-500" type="submit">
                                                        Simpan
                                                    </button>
                                                </form>

                                                <a class="mt-3 inline-flex text-sm font-semibold text-emerald-700" href="{{ route('transactions.show', $order) }}">
                                                    Detail transaksi
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-6 hidden overflow-x-auto lg:block">
                                        <table class="min-w-full text-left text-sm">
                                            <thead>
                                                <tr class="border-b border-slate-200 text-slate-400">
                                                    <th class="px-3 py-3 font-semibold">Invoice</th>
                                                    <th class="px-3 py-3 font-semibold">Buyer</th>
                                                    <th class="px-3 py-3 font-semibold">Produk</th>
                                                    <th class="px-3 py-3 font-semibold">Status</th>
                                                    <th class="px-3 py-3 font-semibold">Nominal</th>
                                                    <th class="px-3 py-3 font-semibold">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200">
                                                @foreach ($recentOrders as $order)
                                                    @php
                                                        $statusClass = match ($order->status) {
                                                            'Selesai' => 'bg-emerald-50 text-emerald-700',
                                                            'Perlu cek chat' => 'bg-amber-50 text-amber-700',
                                                            'Dibatalkan' => 'bg-rose-50 text-rose-700',
                                                            'Diproses' => 'bg-sky-50 text-sky-700',
                                                            default => 'bg-slate-100 text-slate-700',
                                                        };
                                                    @endphp
                                                    <tr class="align-top">
                                                        <td class="px-3 py-4">
                                                            <p class="font-semibold text-slate-900">{{ $order->reference }}</p>
                                                            <p class="mt-1 text-xs text-slate-400">{{ $order->ordered_at?->format('d M Y H:i') }}</p>
                                                        </td>
                                                        <td class="px-3 py-4 text-slate-700">{{ $order->buyer?->name }}</td>
                                                        <td class="px-3 py-4">
                                                            <p class="font-medium text-slate-900">{{ $order->product_name }}</p>
                                                            <p class="mt-1 text-xs text-slate-400">{{ $order->game_title }} • Qty {{ $order->quantity }}</p>
                                                        </td>
                                                        <td class="px-3 py-4">
                                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $order->status }}</span>
                                                        </td>
                                                        <td class="px-3 py-4 font-bold text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                                        <td class="px-3 py-4">
                                                            <div class="space-y-3">
                                                                <form action="{{ route('seller.transactions.update-status', $order) }}" class="grid gap-2 xl:grid-cols-[1fr,auto]" method="POST">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <select class="h-10 rounded-[14px] border border-slate-200 bg-white px-3 text-xs text-slate-700 outline-none" name="status">
                                                                        @foreach ($availableStatuses as $status)
                                                                            <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <button class="rounded-[14px] bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-500" type="submit">
                                                                        Simpan
                                                                    </button>
                                                                </form>
                                                                <a class="inline-flex text-xs font-semibold text-emerald-700" href="{{ route('transactions.show', $order) }}">
                                                                    Detail transaksi
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="mt-6 rounded-[22px] border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500">
                                        Belum ada order masuk. Setelah buyer checkout, data transaksi akan muncul di sini.
                                    </div>
                                @endif
                            </article>

                            <div class="space-y-5">
                                <article class="softdash-card p-6 lg:p-7">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Status breakdown</h2>
                                            <p class="mt-2 text-sm text-slate-500">Distribusi order berdasarkan status transaksi.</p>
                                        </div>
                                    </div>

                                    <div class="mt-6 space-y-3">
                                        @foreach ($orderBreakdown as $index => $item)
                                            <div class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4">
                                                <div class="flex items-center justify-between gap-4">
                                                    <div class="flex items-center gap-3">
                                                        <span class="h-3 w-3 rounded-full" style="background-color: {{ $donutColors[$index % count($donutColors)] }}"></span>
                                                        <span class="font-semibold text-slate-800">{{ $item['label'] }}</span>
                                                    </div>
                                                    <span class="text-lg font-black text-slate-900">{{ $item['value'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </article>

                                <article class="softdash-card p-6 lg:p-7">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Panduan seller</h2>
                                            <p class="mt-2 text-sm text-slate-500">Checklist singkat saat menangani order yang masuk.</p>
                                        </div>
                                    </div>

                                    <div class="mt-6 space-y-3">
                                        @foreach ($statusGuides as $guide)
                                            <div class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4 text-sm leading-7 text-slate-600">
                                                {{ $guide }}
                                            </div>
                                        @endforeach
                                    </div>
                                </article>
                            </div>
                        </section>
                    </section>

                    <section
                        class="space-y-5"
                        data-panel-copy="Kelola inventori toko, pantau stok kritis, dan buka listing yang perlu diperbarui tanpa pindah ke halaman lain."
                        data-panel-kicker="Listing"
                        data-panel-title="Inventory workspace"
                        data-workspace-panel="listing"
                        hidden
                    >
                        <section class="grid gap-4 xl:grid-cols-4">
                            @foreach ($listingHighlights as $stat)
                                <article class="softdash-stat">
                                    <p class="text-sm text-slate-400">{{ $stat['label'] }}</p>
                                    <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $stat['value'] }}</p>
                                    <p class="mt-3 text-sm leading-6 text-slate-500">{{ $stat['note'] }}</p>
                                </article>
                            @endforeach
                        </section>

                        <section class="grid gap-5 2xl:grid-cols-[0.92fr,1.08fr]">
                            <article class="softdash-card p-6 lg:p-7">
                                <div class="flex items-end justify-between gap-4">
                                    <div>
                                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Low stock</h2>
                                        <p class="mt-2 text-sm text-slate-500">Listing yang perlu diisi ulang.</p>
                                    </div>
                                    <a class="text-sm font-semibold text-emerald-700" href="{{ route('seller.products.create') }}">Tambah produk</a>
                                </div>

                                <div class="mt-6 space-y-3">
                                    @forelse ($lowStockProducts as $product)
                                        <div class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4">
                                            <div class="flex items-center justify-between gap-4">
                                                <div>
                                                    <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                                    <p class="mt-2 text-sm text-slate-500">{{ $product->game_title }} • {{ $product->type }}</p>
                                                </div>
                                                <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-700">Stok {{ $product->stock }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-[20px] border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500">
                                            Tidak ada listing aktif dengan stok kritis.
                                        </div>
                                    @endforelse
                                </div>
                            </article>

                            <article class="softdash-card p-6 lg:p-7">
                                <div class="flex items-end justify-between gap-4">
                                    <div>
                                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Listing overview</h2>
                                        <p class="mt-2 text-sm text-slate-500">Produk yang sedang Anda jual dan performanya.</p>
                                    </div>
                                    <a class="text-sm font-semibold text-emerald-700" href="{{ route('seller.products.index') }}">Kelola lengkap</a>
                                </div>

                                <div class="mt-6 grid gap-4 md:grid-cols-2">
                                    @forelse ($inventory as $product)
                                        <article class="rounded-[22px] border border-slate-200 bg-slate-50 p-5">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex h-14 w-14 items-center justify-center rounded-[18px] bg-gradient-to-br {{ $product['tone'] }} text-base font-black text-white">
                                                    {{ $product['thumb'] }}
                                                </div>
                                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product['is_active'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                                    {{ $product['is_active'] ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </div>

                                            <p class="mt-4 text-xs uppercase tracking-[0.22em] text-slate-400">{{ $product['game'] }}</p>
                                            <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $product['name'] }}</h3>

                                            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                                <div>
                                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Harga</p>
                                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $product['price'] }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Stok</p>
                                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $product['stock'] }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Terjual</p>
                                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ number_format($product['sold_count'], 0, ',', '.') }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-5 flex gap-2">
                                                <a class="inline-flex flex-1 items-center justify-center rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100" href="{{ route('seller.products.edit', $product['slug']) }}">
                                                    Edit
                                                </a>
                                                <a class="inline-flex flex-1 items-center justify-center rounded-[16px] bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500" href="{{ route('products.show', $product['slug']) }}">
                                                    Lihat
                                                </a>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="rounded-[22px] border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500 md:col-span-2">
                                            Belum ada listing. Mulai dari tombol "Buat listing baru" di atas.
                                        </div>
                                    @endforelse
                                </div>
                            </article>
                        </section>
                    </section>

                    <section
                        class="space-y-5"
                        data-panel-copy="Preview bagaimana toko Anda tampil di sisi buyer, termasuk listing live dan sinyal kepercayaan storefront."
                        data-panel-kicker="Storefront"
                        data-panel-title="Buyer-facing preview"
                        data-workspace-panel="storefront"
                        hidden
                    >
                        <section class="grid gap-4 xl:grid-cols-4">
                            @foreach ($storefrontHighlights as $stat)
                                <article class="softdash-stat">
                                    <p class="text-sm text-slate-400">{{ $stat['label'] }}</p>
                                    <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $stat['value'] }}</p>
                                    <p class="mt-3 text-sm leading-6 text-slate-500">{{ $stat['note'] }}</p>
                                </article>
                            @endforeach
                        </section>

                        <section class="grid gap-5 xl:grid-cols-[0.92fr,1.08fr]">
                            <article class="softdash-card p-6 lg:p-7">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                                    <div>
                                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Storefront snapshot</h2>
                                        <p class="mt-2 text-sm text-slate-500">Ringkasan bagaimana toko ini tampil di halaman buyer tanpa keluar dari dashboard.</p>
                                    </div>
                                    <span class="softdash-pill">{{ $storeOverview['active_listings'] }} listing aktif</span>
                                </div>

                                <div class="mt-6 grid gap-4 md:grid-cols-3">
                                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-5">
                                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Status etalase</p>
                                        <p class="mt-3 text-2xl font-black text-slate-900">Live</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-500">Listing aktif seller ini otomatis bisa muncul di katalog buyer.</p>
                                    </div>
                                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-5">
                                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Produk aktif</p>
                                        <p class="mt-3 text-2xl font-black text-slate-900">{{ $storeOverview['active_listings'] }}</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-500">Jumlah listing yang siap terlihat oleh buyer saat ini.</p>
                                    </div>
                                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-5">
                                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Fokus buyer</p>
                                        <p class="mt-3 text-2xl font-black text-slate-900">{{ count($featuredInventory) }}</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-500">Produk teratas yang bisa Anda sorot dari panel listing.</p>
                                    </div>
                                </div>
                            </article>

                            <article class="softdash-card p-6 lg:p-7">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Storefront notes</h2>
                                        <p class="mt-2 text-sm text-slate-500">Hal yang paling mempengaruhi tampilan listing di buyer.</p>
                                    </div>
                                    <a class="text-sm font-semibold text-emerald-700" href="{{ route('home') }}">Lihat buyer</a>
                                </div>

                                <div class="mt-6 space-y-3">
                                    @foreach ($storefrontGuides as $guide)
                                        <div class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4 text-sm leading-7 text-slate-600">
                                            {{ $guide }}
                                        </div>
                                    @endforeach
                                </div>

                                <button
                                    class="mt-6 w-full rounded-[18px] bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-500"
                                    data-workspace-label="Listing"
                                    data-workspace-trigger="listing"
                                    type="button"
                                >
                                    Kembali ke listing
                                </button>
                            </article>
                        </section>

                        <section class="softdash-card p-6 lg:p-7">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Produk yang tampil di buyer</h2>
                                    <p class="mt-2 text-sm text-slate-500">Preview listing utama seller yang kemungkinan paling sering dilihat buyer.</p>
                                </div>
                                <button
                                    class="softdash-pill"
                                    data-workspace-label="Listing"
                                    data-workspace-trigger="listing"
                                    type="button"
                                >
                                    Kelola produk
                                </button>
                            </div>

                            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                @forelse ($featuredInventory as $product)
                                    <article class="rounded-[22px] border border-slate-200 bg-slate-50 p-5">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex h-14 w-14 items-center justify-center rounded-[18px] bg-gradient-to-br {{ $product['tone'] }} text-base font-black text-white">
                                                {{ $product['thumb'] }}
                                            </div>
                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 shadow-sm">{{ $product['rating'] }}</span>
                                        </div>

                                        <p class="mt-4 text-xs uppercase tracking-[0.22em] text-slate-400">{{ $product['game'] }}</p>
                                        <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $product['name'] }}</h3>
                                        <p class="mt-2 text-sm text-slate-500">{{ $product['seller'] }} • {{ $product['region'] }}</p>

                                        <div class="mt-4 rounded-[18px] border border-slate-200 bg-white px-4 py-4">
                                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Harga mulai</p>
                                            <p class="mt-1 text-lg font-black text-slate-900">{{ $product['price'] }}</p>
                                        </div>
                                    </article>
                                @empty
                                    <div class="rounded-[22px] border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500 md:col-span-2 xl:col-span-4">
                                        Belum ada listing yang bisa dipreview di storefront.
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </section>
                </div>
            </main>
        </div>
    </div>
@endsection
