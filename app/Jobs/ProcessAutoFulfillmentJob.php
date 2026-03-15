<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Support\TransactionFlow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class ProcessAutoFulfillmentJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Transaction $transaction)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulate external API delay
        sleep(5);

        // Lock the transaction to prevent race conditions during fulfillment
        $lockedTransaction = Transaction::query()->lockForUpdate()->find($this->transaction->id);

        if ($lockedTransaction->status !== 'Diproses') {
            return; // Only process if still in "Diproses" status
        }

        // Generate a fake voucher code
        $voucherCode = strtoupper(Str::random(12));

        // 1. Send the voucher code as a message from the seller
        $newMessage = $lockedTransaction->messages()->create([
            'user_id' => $lockedTransaction->seller_id,
            'message' => "Pesanan otomatis diproses. Berikut adalah kode voucher Anda:\n\n**{$voucherCode}**\n\nTerima kasih atas pembelian Anda!",
        ]);

        // Broadcast the message so it appears immediately on buyer's screen
        \App\Events\MessageSent::dispatch($lockedTransaction->id, [
            'id' => $newMessage->id,
            'user_id' => $lockedTransaction->seller_id,
            'user_name' => $lockedTransaction->seller->store_name ?: $lockedTransaction->seller->name,
            'message' => $newMessage->message,
            'created_at' => $newMessage->created_at->translatedFormat('d M Y, H:i'),
        ]);

        // 2. Mark the transaction as completed
        TransactionFlow::completeByBuyer($lockedTransaction);
    }
}
