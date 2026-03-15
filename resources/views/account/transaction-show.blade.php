@extends('layouts.app')

@php($bodyClass = 'bg-slate-100 text-slate-900')

@section('content')
    <main class="page-section lg:py-12">
        <section class="page-hero">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="page-kicker">Detail Transaksi</p>
                    <h1 class="page-title">{{ $transaction->reference }}</h1>
                    <p class="page-copy">
                        Invoice, status order, timeline, dan chat buyer-seller sekarang terpusat dalam satu halaman transaksi yang lebih mudah dipantau.
                    </p>
                </div>

                <div class="page-actions">
                    @if ($viewer->isAdmin())
                        <a class="toolbar-pill" href="{{ route('admin.dashboard') }}">Kembali ke dashboard</a>
                    @else
                        <a class="toolbar-pill" href="{{ route('transactions.index') }}">Kembali ke riwayat</a>
                    @endif
                    @if ($viewer->isSeller())
                        <a class="toolbar-pill" href="{{ route('seller.dashboard') }}">Dashboard seller</a>
                    @elseif ($viewer->isAdmin())
                        <a class="toolbar-pill" href="{{ route('admin.dashboard') }}">Dashboard admin</a>
                    @else
                        <a class="toolbar-pill" href="{{ route('home') }}">Kembali ke storefront</a>
                    @endif
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-6 xl:grid-cols-[1.12fr,0.88fr]">
            <div class="space-y-6">
                <div class="surface-card">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="page-kicker">{{ $transaction->product_type }}</p>
                            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">{{ $transaction->product_name }}</h2>
                            <p class="mt-2 text-sm text-slate-600">{{ $transaction->game_title }} • Qty {{ $transaction->quantity }} • {{ $transaction->payment_method }}</p>
                        </div>
                        <span class="rounded-full px-4 py-2 text-sm font-bold {{ match ($transaction->status) {
                            'Selesai' => 'bg-emerald-100 text-emerald-700',
                            'Perlu cek chat' => 'bg-amber-100 text-amber-700',
                            'Dibatalkan' => 'bg-rose-100 text-rose-700',
                            'Diproses' => 'bg-blue-100 text-blue-700',
                            default => 'bg-slate-100 text-slate-700',
                        } }}">
                            {{ $transaction->status }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="surface-card-muted">
                            <p class="eyebrow-label">Buyer</p>
                            <p class="mt-3 text-lg font-extrabold text-slate-900">{{ $transaction->buyer?->name }}</p>
                            <p class="mt-2 text-sm text-slate-500">{{ $transaction->buyer?->email }}</p>
                            <p class="mt-2 text-sm text-slate-500">Game ID: {{ $transaction->game_user_id ?: '-' }}</p>
                        </div>
                        <div class="surface-card-muted">
                            <p class="eyebrow-label">Seller</p>
                            <p class="mt-3 text-lg font-extrabold text-slate-900">{{ $transaction->seller?->store_name ?: $transaction->seller?->name }}</p>
                            <p class="mt-2 text-sm text-slate-500">{{ $transaction->seller?->email }}</p>
                            <p class="mt-2 text-sm text-slate-500">Estimasi kirim: {{ $transaction->meta['delivery'] ?? '-' }}</p>
                        </div>
                    </div>

                    @if ($transaction->buyer_note)
                        <div class="mt-6 rounded-[24px] bg-amber-50 p-5 text-sm leading-7 text-amber-800 ring-1 ring-amber-200">
                            <p class="font-bold text-amber-900">Catatan buyer</p>
                            <p class="mt-2">{{ $transaction->buyer_note }}</p>
                        </div>
                    @endif
                </div>

                <div class="surface-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="page-kicker">Invoice</p>
                            <h2 class="section-title">Ringkasan pembayaran</h2>
                        </div>
                        @if ($transaction->product)
                            <a class="subtle-link" href="{{ route('products.show', $transaction->product) }}">Buka listing</a>
                        @endif
                    </div>

                    <div class="mt-6 space-y-3 rounded-[24px] border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-center justify-between gap-4 text-sm text-slate-600">
                            <span>Harga satuan</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($transaction->meta['unit_price'] ?? $transaction->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm text-slate-600">
                            <span>Jumlah</span>
                            <span class="font-bold text-slate-900">{{ $transaction->quantity }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm text-slate-600">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($transaction->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm text-slate-600">
                            <span>Fee platform</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($transaction->fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-4 text-base">
                            <span class="font-bold text-slate-900">Total dibayar buyer</span>
                            <span class="text-2xl font-black text-brand-700">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm text-slate-600">
                            <span>Saldo bersih seller</span>
                            <span class="font-bold text-emerald-700">Rp {{ number_format(max(0, $transaction->total - $transaction->fee), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="surface-card">
                    <p class="page-kicker">Timeline</p>
                    <h2 class="section-title">Perjalanan order</h2>

                    <div class="mt-6 space-y-4">
                        @foreach ($statusTimeline as $item)
                            <div class="flex gap-4">
                                <div class="mt-1 h-3 w-3 rounded-full bg-brand-600"></div>
                                <div class="flex-1 rounded-[22px] border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <p class="font-bold text-slate-900">{{ $item['title'] }}</p>
                                        <p class="text-sm text-slate-500">{{ $item['time']?->format('d M Y H:i') ?: '-' }}</p>
                                    </div>
                                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ $item['note'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="surface-card">
                    <p class="page-kicker">Aksi</p>
                    <h2 class="section-title">Kontrol transaksi</h2>

                    <div class="mt-6 space-y-4">
                        @if ($transaction->status === 'Menunggu Pembayaran' && $transaction->snap_token)
                            <button id="pay-button" class="w-full rounded-[18px] bg-brand-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-brand-500">
                                Lanjutkan Pembayaran (Midtrans)
                            </button>
                        @endif

                        @if ($canCancel)
                            <form action="{{ route('transactions.buyer-action', $transaction) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input name="action" type="hidden" value="cancel">
                                <button class="w-full rounded-[18px] bg-rose-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-500" type="submit">
                                    Batalkan transaksi
                                </button>
                            </form>
                        @endif

                        @if ($canComplete)
                            <form action="{{ route('transactions.buyer-action', $transaction) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input name="action" type="hidden" value="complete">
                                <button class="w-full rounded-[18px] bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-500" type="submit">
                                    Konfirmasi pesanan selesai
                                </button>
                            </form>
                        @endif

                        @if ($canManageAsSeller)
                            <form action="{{ route('seller.transactions.update-status', $transaction) }}" class="space-y-3 rounded-[24px] border border-slate-200 bg-slate-50 p-5" method="POST">
                                @csrf
                                @method('PATCH')
                                <label class="field-label" for="status">Update status seller</label>
                                <select class="field-select" id="status" name="status">
                                    @foreach ($availableStatuses as $status)
                                        <option value="{{ $status }}" @selected($transaction->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <button class="primary-btn h-[52px] w-full justify-center" type="submit">Simpan status</button>
                            </form>
                        @endif

                        @if (! $canCancel && ! $canComplete && ! $canManageAsSeller)
                            @if ($transaction->status === 'Selesai' && $viewer->id === $transaction->buyer_id)
                                @if ($transaction->review)
                                    <div class="rounded-[24px] border border-emerald-200 bg-emerald-50 p-5 text-sm">
                                        <p class="font-bold text-emerald-900">Ulasan Anda ({{ $transaction->review->rating }} Bintang)</p>
                                        <p class="mt-2 text-emerald-800">{{ $transaction->review->comment ?: '-' }}</p>
                                    </div>
                                @else
                                    <form action="{{ route('transactions.review.store', $transaction) }}" class="space-y-4 rounded-[24px] border border-slate-200 bg-slate-50 p-5" method="POST">
                                        @csrf
                                        <div>
                                            <label class="field-label" for="rating">Berikan Rating Produk</label>
                                            <select class="field-select" id="rating" name="rating" required>
                                                <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                                                <option value="4">⭐⭐⭐⭐ (Puas)</option>
                                                <option value="3">⭐⭐⭐ (Biasa Saja)</option>
                                                <option value="2">⭐⭐ (Kurang Puas)</option>
                                                <option value="1">⭐ (Sangat Kecewa)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="field-label" for="comment">Ulasan (Opsional)</label>
                                            <textarea class="field-textarea rounded-[18px] text-sm" id="comment" name="comment" rows="3" placeholder="Bagaimana pengalaman Anda membeli produk ini?"></textarea>
                                        </div>
                                        <button class="primary-btn h-[52px] w-full justify-center" type="submit">Kirim Ulasan</button>
                                    </form>
                                @endif
                            @else
                                <div class="surface-card-muted text-sm text-slate-600">
                                    Tidak ada aksi operasional khusus untuk role ini pada status transaksi saat ini.
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="surface-card">
                    <p class="page-kicker">Chat Order</p>
                    <h2 class="section-title">Komunikasi buyer dan seller</h2>

                    <div class="mt-6 space-y-4" id="chat-container">
                        @forelse ($transaction->messages as $message)
                            @php($isOwnMessage = $message->user_id === $viewer->id)
                            <article class="rounded-[24px] p-4 {{ $isOwnMessage ? 'bg-brand-700 text-white' : 'bg-slate-50 text-slate-900 border border-slate-200' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-bold">{{ $message->user?->store_name ?: $message->user?->name }}</p>
                                    </div>
                                    <p class="text-xs {{ $isOwnMessage ? 'text-blue-100' : 'text-slate-400' }}">{{ $message->created_at?->format('d M Y H:i') }}</p>
                                </div>
                                <p class="mt-4 text-sm leading-7 {{ $isOwnMessage ? 'text-white' : 'text-slate-600' }}">{{ $message->message }}</p>
                            </article>
                        @empty
                            <div class="rounded-[24px] bg-slate-50 p-5 text-sm text-slate-600 border border-slate-200" id="empty-chat">
                                Belum ada pesan. Buyer dan seller bisa menggunakan chat ini untuk klarifikasi order.
                            </div>
                        @endforelse
                    </div>

                    <form action="{{ route('transactions.messages.store', $transaction) }}" class="mt-6 space-y-3" method="POST">
                        @csrf
                        <label class="field-label" for="message">Kirim pesan</label>
                        <textarea class="field-textarea rounded-[22px] py-4 text-sm" id="message" name="message" rows="4" placeholder="Tulis pesan terkait order ini...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-sm font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                        <button class="primary-btn h-[52px] w-full justify-center" type="submit">Kirim pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            const currentUserId = {{ $viewer->id }};
            const chatContainer = document.getElementById('chat-container');
            const emptyChat = document.getElementById('empty-chat');

            window.Echo.private('transaction.{{ $transaction->id }}')
                .listen('MessageSent', (e) => {
                    const isOwnMessage = e.messageData.user_id === currentUserId;
                    const bgClass = isOwnMessage ? 'bg-brand-700 text-white' : 'bg-slate-50 text-slate-900 border border-slate-200';
                    const timeClass = isOwnMessage ? 'text-blue-100' : 'text-slate-400';
                    const textClass = isOwnMessage ? 'text-white' : 'text-slate-600';

                    const newBubble = document.createElement('article');
                    newBubble.className = `rounded-[24px] p-4 ${bgClass}`;
                    newBubble.innerHTML = `
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-bold">${e.messageData.user_name}</p>
                            </div>
                            <p class="text-xs ${timeClass}">${e.messageData.created_at}</p>
                        </div>
                        <p class="mt-4 text-sm leading-7 ${textClass}">${e.messageData.message}</p>
                    `;

                    if (emptyChat) emptyChat.remove();
                    chatContainer.appendChild(newBubble);
                    
                    // Auto-scroll
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                });
        });
    </script>
@endpush

@if ($transaction->status === 'Menunggu Pembayaran' && $transaction->snap_token)
    @push('head')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endpush
    @push('scripts')
        <script>
            document.getElementById('pay-button').onclick = function () {
                snap.pay('{{ $transaction->snap_token }}', {
                    onSuccess: function (result) {
                        alert("Pembayaran berhasil! Sistem sedang memproses...");
                        window.location.reload();
                    },
                    onPending: function (result) {
                        alert("Menunggu pembayaran Anda!");
                    },
                    onError: function (result) {
                        alert("Gagal melakukan pembayaran");
                    },
                    onClose: function () {
                        // User ditutup modalnya
                    }
                });
            };
        </script>
    @endpush
@endif
