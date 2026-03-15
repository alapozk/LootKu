<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Support\MarketplaceUi;
use App\Support\TransactionFlow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function show(Request $request, Transaction $transaction): View
    {
        $user = $request->user();
        abort_unless($this->canAccessTransaction($user->id, $user->role, $transaction), 403);

        $transaction->loadMissing([
            'buyer',
            'seller',
            'product',
            'review',
            'messages' => fn ($query) => $query->latest(),
            'messages.user',
        ]);

        return view('account.transaction-show', [
            'pageTitle' => 'Detail Transaksi '.$transaction->reference,
            'transaction' => $transaction,
            'viewer' => $user,
            'availableStatuses' => MarketplaceUi::availableStatuses(),
            'statusTimeline' => $this->timelineFor($transaction),
            'canCancel' => $user->id === $transaction->buyer_id && TransactionFlow::buyerCanCancel($transaction),
            'canComplete' => $user->id === $transaction->buyer_id && TransactionFlow::buyerCanComplete($transaction),
            'canManageAsSeller' => $user->id === $transaction->seller_id,
        ]);
    }

    public function buyerAction(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($request->user()->id === $transaction->buyer_id, 403);

        $validated = $request->validate([
            'action' => ['required', Rule::in(['cancel', 'complete'])],
        ]);

        $updated = match ($validated['action']) {
            'cancel' => TransactionFlow::cancelByBuyer($transaction),
            'complete' => TransactionFlow::completeByBuyer($transaction),
        };

        if (! $updated) {
            return back()->with('error', 'Aksi tidak tersedia untuk status transaksi saat ini.');
        }

        return back()->with(
            'status',
            $validated['action'] === 'cancel'
                ? 'Transaksi dibatalkan dan stok listing dikembalikan.'
                : 'Transaksi dikonfirmasi selesai.'
        );
    }

    public function storeMessage(Request $request, Transaction $transaction): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->canAccessTransaction($user->id, $user->role, $transaction), 403);

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:1200'],
        ]);

        $newMessage = $transaction->messages()->create([
            'user_id' => $user->id,
            'message' => $validated['message'],
        ]);

        \App\Events\MessageSent::dispatch($transaction->id, [
            'id' => $newMessage->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'message' => $newMessage->message,
            'created_at' => $newMessage->created_at->translatedFormat('d M Y, H:i'),
        ]);

        if ($user->id === $transaction->seller_id) {
            TransactionFlow::markNeedsChat($transaction);
        }

        if ($user->id === $transaction->buyer_id) {
            TransactionFlow::markBackToProcessing($transaction);
        }

        return back()->with('status', 'Pesan transaksi berhasil dikirim.');
    }

    public function storeReview(Request $request, Transaction $transaction): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->id === $transaction->buyer_id, 403);
        abort_unless($transaction->status === 'Selesai', 400, 'Transaksi belum selesai.');
        
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        if ($transaction->review()->exists()) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk transaksi ini.');
        }

        $transaction->review()->create([
            'product_id' => $transaction->product_id,
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        // Update product average rating
        $product = $transaction->product;
        if ($product) {
            $averageRating = $product->reviews()->avg('rating');
            $product->update(['rating' => round($averageRating, 1)]);
        }

        return back()->with('status', 'Terima kasih, ulasan Anda berhasil disimpan!');
    }

    private function canAccessTransaction(int $userId, string $role, Transaction $transaction): bool
    {
        return $role === 'admin'
            || $transaction->buyer_id === $userId
            || $transaction->seller_id === $userId;
    }

    private function timelineFor(Transaction $transaction): array
    {
        $events = [
            [
                'title' => 'Order dibuat',
                'time' => $transaction->ordered_at,
                'note' => 'Checkout berhasil dan invoice transaksi tercatat.',
            ],
        ];

        if (in_array($transaction->status, TransactionFlow::paidStatuses(), true)) {
            $events[] = [
                'title' => 'Pembayaran terkonfirmasi',
                'time' => $transaction->ordered_at,
                'note' => 'Dana masuk ke escrow dan seller bisa memproses order.',
            ];
        }

        if ($transaction->status === 'Perlu cek chat') {
            $events[] = [
                'title' => 'Butuh respons buyer',
                'time' => $transaction->updated_at,
                'note' => 'Seller meminta buyer membuka chat transaksi.',
            ];
        }

        if ($transaction->status === 'Dibatalkan') {
            $events[] = [
                'title' => 'Transaksi dibatalkan',
                'time' => $transaction->completed_at ?: $transaction->updated_at,
                'note' => 'Order tidak dilanjutkan dan stok dikembalikan ke listing.',
            ];
        }

        if ($transaction->status === 'Selesai') {
            $events[] = [
                'title' => 'Order selesai',
                'time' => $transaction->completed_at ?: $transaction->updated_at,
                'note' => 'Buyer sudah menerima item dan dana siap dicairkan ke seller.',
            ];
        }

        return $events;
    }
}
