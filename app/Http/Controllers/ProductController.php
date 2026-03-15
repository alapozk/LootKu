<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Support\MarketplaceUi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Request $request, Product $product): View
    {
        abort_unless($product->is_active || $request->user()?->id === $product->seller_id, 404);

        $relatedProducts = Product::query()
            ->with('seller')
            ->where('is_active', true)
            ->whereKeyNot($product->id)
            ->where(function ($builder) use ($product) {
                $builder->where('type', $product->type)
                    ->orWhere('game_title', $product->game_title);
            })
            ->orderByDesc('sold_count')
            ->take(4)
            ->get()
            ->map(fn (Product $relatedProduct) => MarketplaceUi::productToCard($relatedProduct))
            ->all();

        return view('buyer.product-show', [
            'pageTitle' => $product->name.' | Lootku Market',
            'topTags' => MarketplaceUi::topTags(),
            'navigation' => MarketplaceUi::navigation(),
            'product' => MarketplaceUi::productToCard($product->load('seller')),
            'productModel' => $product->load('seller'),
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function showCheckout(Product $product): View
    {
        abort_unless($product->is_active, 404);

        return view('buyer.checkout', [
            'pageTitle' => 'Checkout '.$product->name,
            'topTags' => MarketplaceUi::topTags(),
            'navigation' => MarketplaceUi::navigation(),
            'product' => MarketplaceUi::productToCard($product->load('seller')),
            'productModel' => $product->load('seller'),
            'paymentMethods' => MarketplaceUi::availablePaymentMethods(),
        ]);
    }

    public function checkout(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        if ($request->user()->id === $product->seller_id) {
            return back()->with('error', 'Seller tidak bisa membeli listing miliknya sendiri.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'payment_method' => ['required', Rule::in(MarketplaceUi::availablePaymentMethods())],
            'game_user_id' => ['required', 'string', 'max:255'],
            'buyer_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $transaction = DB::transaction(function () use ($validated, $request, $product) {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);
            $quantity = (int) $validated['quantity'];

            if ($lockedProduct->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok produk tidak mencukupi untuk jumlah yang dipilih.',
                ]);
            }

            $subtotal = $lockedProduct->price * $quantity;
            $fee = (int) round($subtotal * 0.05);
            $status = $validated['payment_method'] === 'Saldo' ? 'Diproses' : 'Menunggu Pembayaran';

            $transaction = Transaction::query()->create([
                'reference' => 'LKM-'.Str::upper(Str::random(8)),
                'buyer_id' => $request->user()->id,
                'seller_id' => $lockedProduct->seller_id,
                'product_id' => $lockedProduct->id,
                'product_name' => $lockedProduct->name,
                'game_title' => $lockedProduct->game_title,
                'product_type' => $lockedProduct->type,
                'status' => $status,
                'payment_method' => $validated['payment_method'],
                'quantity' => $quantity,
                'game_user_id' => $validated['game_user_id'],
                'buyer_note' => $validated['buyer_note'] ?? null,
                'price' => $subtotal,
                'fee' => $fee,
                'total' => $subtotal,
                'ordered_at' => now(),
                'completed_at' => $status === 'Selesai' ? now() : null,
                'meta' => [
                    'delivery' => $lockedProduct->delivery_estimate,
                    'unit_price' => $lockedProduct->price,
                ],
            ]);

            $lockedProduct->decrement('stock', $quantity);
            $lockedProduct->increment('sold_count', $quantity);

            return $transaction;
        });

        if ($transaction->status === 'Menunggu Pembayaran' && $transaction->payment_method !== 'Saldo') {
            $paymentService = new \App\Services\PaymentGatewayService();
            $transaction->update(['snap_token' => $paymentService->getSnapToken($transaction)]);
        }

        // Trigger Auto-fulfillment simulate if product type is Top Up
        if ($product->type === 'Top Up' && $transaction->status === 'Diproses') {
            \App\Jobs\ProcessAutoFulfillmentJob::dispatch($transaction);
        }

        return redirect()
            ->route('transactions.index')
            ->with('status', 'Checkout berhasil dibuat. Transaksi sudah masuk ke riwayat.');
    }
}
