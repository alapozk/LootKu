<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Support\MarketplaceUi;
use App\Support\TransactionFlow;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $buyersCount = User::query()->where('role', 'buyer')->count();
        $activeProductsCount = Product::query()->where('is_active', true)->count();
        $sellers = User::query()
            ->where('role', 'seller')
            ->with(['products', 'sales'])
            ->get();
        $transactions = Transaction::query()
            ->with(['buyer', 'seller', 'product'])
            ->latest('ordered_at')
            ->get();
        $disputedTransactions = Transaction::query()
            ->with(['buyer', 'seller', 'product'])
            ->where('status', 'Komplain')
            ->latest('updated_at')
            ->get();
        $withdrawals = \App\Models\Withdrawal::query()
            ->with('user')
            ->latest()
            ->get();
        $activeProducts = Product::query()
            ->with('seller')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();
        $lowStockProducts = Product::query()
            ->with('seller')
            ->where('is_active', true)
            ->where('stock', '<=', 5)
            ->latest()
            ->take(5)
            ->get();

        $topSellers = $sellers
            ->map(function (User $seller) {
                $paidSales = $seller->sales->whereIn('status', TransactionFlow::paidStatuses());

                return [
                    'seller' => $seller,
                    'revenue' => (int) $paidSales->sum('total'),
                    'orders' => $seller->sales->count(),
                    'active_listings' => $seller->products->where('is_active', true)->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->take(6)
            ->values();

        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard Admin | Lootku Market',
            'dashboardStats' => [
                ['label' => 'Buyer', 'value' => number_format($buyersCount, 0, ',', '.'), 'note' => 'Akun buyer terdaftar'],
                ['label' => 'Seller', 'value' => number_format($sellers->count(), 0, ',', '.'), 'note' => 'Akun seller aktif'],
                ['label' => 'Produk Aktif', 'value' => number_format($activeProductsCount, 0, ',', '.'), 'note' => 'Total listing aktif marketplace'],
                ['label' => 'GMV Dibayar', 'value' => MarketplaceUi::formatRupiah((int) $transactions->whereIn('status', TransactionFlow::paidStatuses())->sum('total')), 'note' => 'Status diproses, perlu chat, dan selesai'],
            ],
            'statusBreakdown' => collect(MarketplaceUi::availableStatuses())
                ->map(fn (string $status) => [
                    'label' => $status,
                    'value' => $transactions->where('status', $status)->count(),
                ])
                ->all(),
            'recentTransactions' => $transactions->take(8),
            'topSellers' => $topSellers,
            'lowStockProducts' => $lowStockProducts,
            'latestProducts' => $activeProducts,
            'withdrawals' => $withdrawals,
            'disputedTransactions' => $disputedTransactions,
        ]);
    }

    public function updateWithdrawal(\Illuminate\Http\Request $request, \App\Models\Withdrawal $withdrawal)
    {
        $validated = $request->validate([
            'status' => 'required|in:Sukses,Ditolak',
        ]);

        if ($withdrawal->status !== 'Pending') {
            return back()->with('error', 'Penarikan saldo sudah diproses sebelumnya.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $withdrawal) {
            $lockedWithdrawal = \App\Models\Withdrawal::query()->lockForUpdate()->find($withdrawal->id);
            if ($lockedWithdrawal->status !== 'Pending') {
                return;
            }

            $lockedWithdrawal->update([
                'status' => $validated['status'],
                'processed_at' => now(),
            ]);

            if ($validated['status'] === 'Ditolak') {
                // Refund balance to seller
                $seller = User::find($lockedWithdrawal->user_id);
                $seller->increment('balance', $lockedWithdrawal->amount);
            }
        });

        return back()->with('success', 'Status penarikan saldo berhasil diperbarui.');
    }

    public function resolveDispute(\Illuminate\Http\Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'resolution' => 'required|in:seller,buyer',
        ]);

        if ($transaction->status !== 'Komplain') {
            return back()->with('error', 'Transaksi ini tidak dalam status komplain.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $transaction) {
            $lockedTransaction = Transaction::query()->lockForUpdate()->find($transaction->id);
            if ($lockedTransaction->status !== 'Komplain') {
                return;
            }

            if ($validated['resolution'] === 'seller') {
                $lockedTransaction->update([
                    'status' => 'Selesai',
                    'completed_at' => now(),
                ]);

                // Release funds to seller
                $seller = User::query()->lockForUpdate()->find($lockedTransaction->seller_id);
                $payoutAmount = max(0, $lockedTransaction->total - $lockedTransaction->fee);
                $seller->increment('balance', $payoutAmount);

            } elseif ($validated['resolution'] === 'buyer') {
                $lockedTransaction->update([
                    'status' => 'Dibatalkan',
                    'completed_at' => now(),
                ]);

                // Return stock
                $product = Product::query()->lockForUpdate()->find($lockedTransaction->product_id);
                if ($product) {
                    $product->increment('stock', $lockedTransaction->quantity);
                    $product->decrement('sold_count', $lockedTransaction->quantity);
                }
            }
        });

        return back()->with('success', 'Komplain berhasil diselesaikan.');
    }
}
