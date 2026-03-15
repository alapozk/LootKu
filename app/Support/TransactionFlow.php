<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionFlow
{
    public static function allStatuses(): array
    {
        return ['Menunggu Pembayaran', 'Diproses', 'Perlu cek chat', 'Komplain', 'Selesai', 'Dibatalkan'];
    }

    public static function paidStatuses(): array
    {
        return ['Diproses', 'Perlu cek chat', 'Selesai'];
    }

    public static function activeStatuses(): array
    {
        return ['Menunggu Pembayaran', 'Diproses', 'Perlu cek chat'];
    }

    public static function finalStatuses(): array
    {
        return ['Selesai', 'Dibatalkan'];
    }

    public static function sellerManagedStatuses(): array
    {
        return ['Diproses', 'Perlu cek chat', 'Komplain', 'Selesai', 'Dibatalkan'];
    }

    public static function buyerCanCancel(Transaction $transaction): bool
    {
        return $transaction->status === 'Menunggu Pembayaran';
    }

    public static function buyerCanComplete(Transaction $transaction): bool
    {
        return in_array($transaction->status, ['Diproses', 'Perlu cek chat'], true);
    }

    public static function cancelByBuyer(Transaction $transaction): bool
    {
        if (! self::buyerCanCancel($transaction)) {
            return false;
        }

        return self::cancel($transaction);
    }

    public static function completeByBuyer(Transaction $transaction): bool
    {
        if (! self::buyerCanComplete($transaction)) {
            return false;
        }

        return self::markCompleted($transaction);
    }

    public static function updateBySeller(Transaction $transaction, string $status): bool
    {
        if (! in_array($status, self::sellerManagedStatuses(), true)) {
            return false;
        }

        if (in_array($transaction->status, self::finalStatuses(), true)) {
            return false;
        }

        return match ($status) {
            'Selesai' => self::markCompleted($transaction),
            'Dibatalkan' => self::cancel($transaction),
            default => self::setStatus($transaction, $status),
        };
    }

    public static function markNeedsChat(Transaction $transaction): bool
    {
        if (! in_array($transaction->status, ['Diproses', 'Perlu cek chat'], true)) {
            return false;
        }

        return self::setStatus($transaction, 'Perlu cek chat');
    }

    public static function markBackToProcessing(Transaction $transaction): bool
    {
        if ($transaction->status !== 'Perlu cek chat') {
            return false;
        }

        return self::setStatus($transaction, 'Diproses');
    }

    private static function setStatus(Transaction $transaction, string $status): bool
    {
        return (bool) DB::transaction(function () use ($transaction, $status) {
            $lockedTransaction = Transaction::query()->lockForUpdate()->find($transaction->id);

            if (! $lockedTransaction || in_array($lockedTransaction->status, self::finalStatuses(), true)) {
                return false;
            }

            $lockedTransaction->status = $status;
            $lockedTransaction->completed_at = $status === 'Selesai' ? now() : null;
            $lockedTransaction->save();

            return true;
        });
    }

    private static function markCompleted(Transaction $transaction): bool
    {
        return (bool) DB::transaction(function () use ($transaction) {
            $lockedTransaction = Transaction::query()->lockForUpdate()->find($transaction->id);

            if (! $lockedTransaction || ! in_array($lockedTransaction->status, ['Diproses', 'Perlu cek chat'], true)) {
                return false;
            }

            $lockedTransaction->status = 'Selesai';
            $lockedTransaction->completed_at = now();
            $lockedTransaction->save();

            // Release escrow to seller balance
            $seller = \App\Models\User::query()->lockForUpdate()->find($lockedTransaction->seller_id);
            if ($seller) {
                $payoutAmount = max(0, $lockedTransaction->total - $lockedTransaction->fee);
                $seller->increment('balance', $payoutAmount);
            }

            return true;
        });
    }

    private static function cancel(Transaction $transaction): bool
    {
        return (bool) DB::transaction(function () use ($transaction) {
            $lockedTransaction = Transaction::query()->lockForUpdate()->find($transaction->id);

            if (! $lockedTransaction || in_array($lockedTransaction->status, self::finalStatuses(), true)) {
                return false;
            }

            $product = $lockedTransaction->product_id
                ? Product::query()->lockForUpdate()->find($lockedTransaction->product_id)
                : null;

            if ($product) {
                $product->stock += $lockedTransaction->quantity;
                $product->sold_count = max(0, $product->sold_count - $lockedTransaction->quantity);
                $product->save();
            }

            $lockedTransaction->status = 'Dibatalkan';
            $lockedTransaction->completed_at = now();
            $lockedTransaction->save();

            return true;
        });
    }
}
