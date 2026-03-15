<?php

namespace App\Support;

use App\Models\Product;

class MarketplaceUi
{
    public static function navigation(): array
    {
        return [
            ['key' => 'home', 'label' => 'Beranda', 'href' => route('home')],
            ['key' => 'catalog', 'label' => 'Kategori', 'href' => route('catalog')],
            ['key' => 'top-up', 'label' => 'Top Up', 'href' => route('catalog', ['type' => 'Top Up'])],
            ['key' => 'game-key', 'label' => 'Game Key', 'href' => route('catalog', ['type' => 'Game Key'])],
            ['key' => 'akun', 'label' => 'Akun', 'href' => route('catalog', ['type' => 'Akun'])],
            ['key' => 'voucher', 'label' => 'Voucher', 'href' => route('catalog', ['type' => 'Voucher'])],
            ['key' => 'item', 'label' => 'Item', 'href' => route('catalog', ['type' => 'Item'])],
        ];
    }

    public static function topTags(): array
    {
        return [
            'Robux',
            'Steam Wallet',
            'Mobile Legends',
            'Voucher',
            'Akun Blox Fruit',
        ];
    }

    public static function quickCategories(): array
    {
        return [
            ['label' => 'Top Up', 'icon' => 'TP', 'desc' => 'Diamond & currency', 'filter' => 'Top Up'],
            ['label' => 'Game Key', 'icon' => 'GK', 'desc' => 'Steam, Epic, origin key', 'filter' => 'Game Key'],
            ['label' => 'Akun', 'icon' => 'AK', 'desc' => 'Akun siap pakai', 'filter' => 'Akun'],
            ['label' => 'Voucher', 'icon' => 'VC', 'desc' => 'Wallet dan giftcard', 'filter' => 'Voucher'],
            ['label' => 'Item', 'icon' => 'IT', 'desc' => 'Skin, gear, pet', 'filter' => 'Item'],
            ['label' => 'Koin Game', 'icon' => 'KG', 'desc' => 'Gold dan coin server', 'filter' => 'Koin Game'],
            ['label' => 'RPG', 'icon' => 'RPG', 'desc' => 'MMO dan JRPG', 'filter' => 'RPG Games'],
            ['label' => 'Simulasi', 'icon' => 'SIM', 'desc' => 'Sim & sandbox', 'filter' => 'Simulasi'],
        ];
    }

    public static function promoCards(): array
    {
        return [
            [
                'headline' => 'Flash deal Roblox',
                'title' => 'RBL',
                'subtitle' => 'Bonus top up lebih tebal untuk seller item Roblox.',
                'offer' => '25% more Robux',
                'tag' => 'Giftcard',
                'cta' => 'Top Up',
                'type' => 'Top Up',
                'tone' => 'from-slate-950 via-blue-950 to-indigo-700',
            ],
            [
                'headline' => 'Pre-order unggulan',
                'title' => 'MONSTER HUNTER',
                'subtitle' => 'Bundle collector dan key launch day dengan harga miring.',
                'offer' => 'Diskon 14%',
                'tag' => 'Hot Games',
                'cta' => 'Grab It Fast',
                'type' => 'Game Key',
                'tone' => 'from-amber-600 via-orange-500 to-rose-500',
            ],
            [
                'headline' => 'Resident Evil',
                'title' => 'REQUIEM',
                'subtitle' => 'Steam key region SEA dengan stok seller terverifikasi.',
                'offer' => 'Limited Batch',
                'tag' => 'Steam Key',
                'cta' => 'Pre Order',
                'type' => 'Game Key',
                'tone' => 'from-slate-900 via-slate-700 to-slate-500',
            ],
            [
                'headline' => 'Fishing simulator',
                'title' => 'FISH IT',
                'subtitle' => 'Item langka, bait pack, dan akun progres cepat.',
                'offer' => 'Explore rare loot',
                'tag' => 'Trending',
                'cta' => 'Explore',
                'type' => 'Item',
                'tone' => 'from-sky-900 via-cyan-700 to-sky-400',
            ],
        ];
    }

    public static function trustSignals(): array
    {
        return [
            ['icon' => 'SA', 'label' => 'Transaksi aman dengan escrow'],
            ['icon' => 'RB', 'label' => 'Garansi uang kembali'],
            ['icon' => 'CS', 'label' => 'Bantuan customer care 24/7'],
        ];
    }

    public static function sellerPerks(): array
    {
        return [
            'Panel penjual untuk kelola listing, stok, dan harga real-time.',
            'Checkout buyer langsung membuat order dan masuk ke riwayat transaksi.',
            'Ringkasan order aktif dan payout membuat operasional seller lebih cepat.',
        ];
    }

    public static function availableTypes(): array
    {
        return collect(self::quickCategories())
            ->pluck('filter')
            ->values()
            ->all();
    }

    public static function availablePaymentMethods(): array
    {
        return ['Saldo', 'QRIS', 'Virtual Account', 'E-Wallet'];
    }

    public static function availableStatuses(): array
    {
        return TransactionFlow::allStatuses();
    }

    public static function statusTone(string $status): string
    {
        return match ($status) {
            'Selesai' => 'bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/20',
            'Perlu cek chat' => 'bg-amber-500/15 text-amber-300 ring-1 ring-amber-400/20',
            'Dibatalkan' => 'bg-rose-500/15 text-rose-300 ring-1 ring-rose-400/20',
            'Diproses' => 'bg-sky-500/15 text-sky-300 ring-1 ring-sky-400/20',
            default => 'bg-slate-500/15 text-slate-300 ring-1 ring-slate-400/20',
        };
    }

    public static function toneForType(string $type): string
    {
        return match ($type) {
            'Top Up' => 'from-blue-700 via-sky-500 to-cyan-300',
            'Voucher' => 'from-slate-900 via-slate-800 to-blue-700',
            'Akun' => 'from-fuchsia-700 via-violet-600 to-indigo-600',
            'Game Key' => 'from-amber-700 via-orange-600 to-yellow-400',
            'Item' => 'from-cyan-700 via-sky-600 to-blue-400',
            'Koin Game' => 'from-emerald-600 via-green-500 to-lime-300',
            'RPG Games' => 'from-yellow-700 via-amber-500 to-orange-300',
            'Simulasi' => 'from-teal-600 via-sky-500 to-blue-300',
            default => 'from-slate-700 via-slate-500 to-slate-300',
        };
    }

    public static function highlightForType(string $type): string
    {
        return match ($type) {
            'Top Up' => 'Fast Process',
            'Voucher' => 'Best Seller',
            'Akun' => 'Ready Account',
            'Game Key' => 'Limited Batch',
            'Item' => 'Rare Loot',
            'Koin Game' => 'Hot Stock',
            default => 'Trending',
        };
    }

    public static function thumbFor(string $gameTitle, string $type): string
    {
        $basis = trim($gameTitle) !== '' ? $gameTitle : $type;

        return strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $basis), 0, 2)) ?: 'GM';
    }

    public static function formatRupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    public static function formatCompactRupiah(int $amount): string
    {
        if ($amount >= 1000000) {
            return number_format($amount / 1000000, 1, ',', '.').' jt';
        }

        if ($amount >= 1000) {
            return number_format($amount / 1000, 0, ',', '.').' rb';
        }

        return (string) $amount;
    }

    public static function parseTags(?string $tags): array
    {
        if (! $tags) {
            return [];
        }

        return collect(explode(',', $tags))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
    }

    public static function productToCard(Product $product): array
    {
        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'game' => $product->game_title,
            'type' => $product->type,
            'price' => self::formatRupiah($product->price),
            'price_raw' => $product->price,
            'seller' => $product->seller?->store_name ?: $product->seller?->name ?: 'Seller',
            'rating' => number_format((float) $product->rating, 1),
            'stock' => number_format((int) $product->stock, 0, ',', '.').' ready',
            'stock_raw' => (int) $product->stock,
            'delivery' => $product->delivery_estimate,
            'region' => $product->region,
            'highlight' => $product->highlight ?: self::highlightForType($product->type),
            'thumb' => $product->thumb ?: self::thumbFor($product->game_title, $product->type),
            'tone' => $product->tone ?: self::toneForType($product->type),
            'tags' => $product->tags ?? [],
            'description' => $product->description,
            'sold_count' => $product->sold_count,
            'is_active' => (bool) $product->is_active,
        ];
    }
}
