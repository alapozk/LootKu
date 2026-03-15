<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Support\MarketplaceUi;
use App\Support\TransactionFlow;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SellerStatsService
{
    public function getDashboardData(User $seller): array
    {
        // 1. Dapatkan daftar produk milik seller dengan paginasi atau limit (untuk inventory)
        $inventoryQuery = $seller->products()->latest();
        $inventory = (clone $inventoryQuery)->take(50)->get()->map(fn (Product $product) => MarketplaceUi::productToCard($product))->all();
        
        // Agregasi Status Produk melalui DB, bukan memory collection
        $activeListingsCount = (clone $inventoryQuery)->where('is_active', true)->count();
        $inactiveListingsCount = (clone $inventoryQuery)->where('is_active', false)->count();
        $lowStockProducts = (clone $inventoryQuery)->where('is_active', true)->where('stock', '<=', 5)->get();
        $totalProductsCount = (clone $inventoryQuery)->count();

        // 2. Transaksi / Penjualan: ambil yang terbaru (jangan ambil semua ke memory)
        // Ambil 20 order terakhir yang butuh diproses sebagai "recent orders"
        $recentOrders = Transaction::query()
            ->with(['buyer', 'product'])
            ->where('seller_id', $seller->id)
            ->latest('ordered_at')
            ->take(10) // <-- Optimization: Mencegah OOM dari ribuan transaksi
            ->get();

        // Agregasi Status Transaksi melalui DB
        $baseSalesQuery = Transaction::query()->where('seller_id', $seller->id);
        $pendingOrdersCount = (clone $baseSalesQuery)->where('status', 'Menunggu Pembayaran')->count();
        $processingOrdersCount = (clone $baseSalesQuery)->where('status', 'Diproses')->count();
        $needsChatOrdersCount = (clone $baseSalesQuery)->where('status', 'Perlu cek chat')->count();
        $completedOrdersCount = (clone $baseSalesQuery)->where('status', 'Selesai')->count();
        $canceledOrdersCount = (clone $baseSalesQuery)->where('status', 'Dibatalkan')->count();
        $totalOrdersCount = (clone $baseSalesQuery)->count();

        // 3. Kalkulasi Finansial
        $readyPayout = (int) (clone $baseSalesQuery)
            ->where('status', 'Selesai')
            ->selectRaw('SUM(total - fee) as total_payout')
            ->value('total_payout');

        $paidSalesQuery = (clone $baseSalesQuery)->whereIn('status', TransactionFlow::paidStatuses());
        
        $monthlyRevenue = (int) (clone $paidSalesQuery)
            ->where('ordered_at', '>=', now()->subDays(30))
            ->sum('total');

        $paidSalesCount = $paidSalesQuery->count();
        $totalPaidRevenue = $paidSalesQuery->sum('total');
        $avgOrderValue = $paidSalesCount > 0 ? (int) round($totalPaidRevenue / $paidSalesCount) : 0;

        // 4. Wallet Entries & Grafik mingguan (Kalkulasi Memory dari query terbatas 30 hari)
        // Dapatkan semua transaksi berbayar dalam 7 hari terakhir untuk grafik mingguan
        $last7DaysSales = (clone $paidSalesQuery)
            ->where('ordered_at', '>=', now()->subDays(7))
            ->get();

        $walletEntries = $recentOrders->take(4)->map(function (Transaction $transaction) {
            return [
                'title' => $transaction->status === 'Selesai' ? 'Order cair otomatis' : 'Dana tertahan di escrow',
                'time' => $transaction->ordered_at?->translatedFormat('d M Y, H:i'),
                'value' => $transaction->status === 'Selesai'
                    ? MarketplaceUi::formatRupiah((int) max(0, $transaction->total - $transaction->fee))
                    : MarketplaceUi::formatRupiah((int) $transaction->total),
                'sort_time' => $transaction->ordered_at,
            ];
        });

        $withdrawals = \App\Models\Withdrawal::where('user_id', $seller->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($w) {
                $statusLabel = $w->status === 'Pending' ? ' (Proses)' : ($w->status === 'Ditolak' ? ' (Ditolak)' : '');
                return [
                    'title' => 'Tarik Saldo ' . $w->bank_name . $statusLabel,
                    'time' => $w->created_at?->translatedFormat('d M Y, H:i'),
                    'value' => '- ' . MarketplaceUi::formatRupiah((int) $w->amount),
                    'sort_time' => $w->created_at,
                ];
            });

        $mergedWalletEntries = $walletEntries->concat($withdrawals)
            ->sortByDesc('sort_time')
            ->take(5)
            ->values()
            ->all();

        return [
            'dashboardStats' => [
                ['label' => 'Omzet 30 Hari', 'value' => MarketplaceUi::formatRupiah($monthlyRevenue), 'delta' => $paidSalesCount.' transaksi berbayar'],
                ['label' => 'Order Aktif', 'value' => (string) ($pendingOrdersCount + $processingOrdersCount + $needsChatOrdersCount), 'delta' => $processingOrdersCount.' sedang diproses'],
                ['label' => 'Saldo Dompet Toko', 'value' => MarketplaceUi::formatRupiah((int) ($seller->balance ?? 0)), 'delta' => 'Siap ditarik ke bank'],
                ['label' => 'Rata-rata Order', 'value' => MarketplaceUi::formatRupiah($avgOrderValue), 'delta' => $completedOrdersCount.' order selesai'],
            ],
            'weeklySales' => $this->weeklySales($last7DaysSales),
            'recentOrders' => $recentOrders->take(6),
            'inventory' => $inventory,
            'walletEntries' => $mergedWalletEntries,
            'storeOverview' => [
                'name' => $seller->store_name ?: $seller->name,
                'joined_at' => $seller->created_at?->translatedFormat('d M Y') ?: '-',
                'active_listings' => $activeListingsCount,
                'inactive_listings' => $inactiveListingsCount,
                'total_orders' => $totalOrdersCount,
            ],
            'actionItems' => [
                ['label' => 'Stok menipis', 'value' => $lowStockProducts->count(), 'note' => 'Listing aktif dengan stok 5 atau kurang'],
                ['label' => 'Menunggu pembayaran', 'value' => $pendingOrdersCount, 'note' => 'Transaksi belum dikonfirmasi buyer'],
                ['label' => 'Perlu cek chat', 'value' => $needsChatOrdersCount, 'note' => 'Order butuh respons seller'],
                ['label' => 'Listing nonaktif', 'value' => $inactiveListingsCount, 'note' => 'Bisa diaktifkan kembali kapan saja'],
            ],
            'lowStockProducts' => $lowStockProducts,
            'orderBreakdown' => [
                ['label' => 'Menunggu Pembayaran', 'value' => $pendingOrdersCount],
                ['label' => 'Diproses', 'value' => $processingOrdersCount],
                ['label' => 'Perlu cek chat', 'value' => $needsChatOrdersCount],
                ['label' => 'Selesai', 'value' => $completedOrdersCount],
                ['label' => 'Dibatalkan', 'value' => $canceledOrdersCount],
            ],
            'availableStatuses' => MarketplaceUi::availableStatuses(),
        ];
    }

    private function weeklySales(Collection $sales): array
    {
        $days = collect(range(6, 0))->map(function (int $offset) use ($sales) {
            $date = Carbon::now()->subDays($offset);
            $dailyTotal = $sales
                ->filter(fn (Transaction $transaction) => $transaction->ordered_at?->isSameDay($date))
                ->sum('total');

            return [
                'day' => $date->translatedFormat('D'),
                'raw_total' => (int) $dailyTotal,
            ];
        });

        $peak = max(1, (int) $days->max('raw_total'));

        return $days->map(function (array $day) use ($peak) {
            return [
                'day' => $day['day'],
                'height' => max(16, (int) round(($day['raw_total'] / $peak) * 100)),
                'value' => $day['raw_total'] > 0 ? MarketplaceUi::formatCompactRupiah($day['raw_total']) : '0',
            ];
        })->all();
    }
}
