<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Support\MarketplaceUi;
use App\Support\TransactionFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TransactionHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $purchases = Transaction::query()
            ->with(['seller', 'product'])
            ->where('buyer_id', $user->id)
            ->latest('ordered_at')
            ->get();

        $sales = $user->role === 'seller'
            ? Transaction::query()
                ->with(['buyer', 'product'])
                ->where('seller_id', $user->id)
                ->latest('ordered_at')
                ->get()
            : collect();

        return view('account.transactions', [
            'pageTitle' => 'Riwayat Transaksi | Lootku Market',
            'purchases' => $purchases,
            'sales' => $sales,
            'purchaseSummary' => $this->summaryFor($purchases, 'buyer'),
            'salesSummary' => $this->summaryFor($sales, 'seller'),
            'availableStatuses' => MarketplaceUi::availableStatuses(),
        ]);
    }

    private function summaryFor(Collection $transactions, string $mode): array
    {
        return [
            'role' => $mode,
            'count' => $transactions->count(),
            'active_count' => $transactions->whereIn('status', TransactionFlow::activeStatuses())->count(),
            'completed_count' => $transactions->where('status', 'Selesai')->count(),
            'gross_total' => $transactions
                ->filter(fn (Transaction $transaction) => in_array($transaction->status, TransactionFlow::paidStatuses(), true))
                ->sum('total'),
        ];
    }
}
