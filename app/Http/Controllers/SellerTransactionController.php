<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Support\MarketplaceUi;
use App\Support\TransactionFlow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SellerTransactionController extends Controller
{
    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($transaction->seller_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(MarketplaceUi::availableStatuses())],
        ]);

        if (! TransactionFlow::updateBySeller($transaction, $validated['status'])) {
            return back()->with('error', 'Perubahan status tidak valid untuk transaksi ini.');
        }

        return back()->with('status', 'Status transaksi berhasil diperbarui.');
    }
}
