<?php

namespace App\Services;

use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentGatewayService
{
    public function __construct()
    {
        // Set your Merchant Server Key
        Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = config('midtrans.is_production');
        // Set sanitization on (default)
        Config::$isSanitized = config('midtrans.is_sanitized');
        // Set 3DS transaction for credit card to true
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function getSnapToken(Transaction $transaction): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $transaction->reference,
                'gross_amount' => $transaction->total,
            ],
            'item_details' => [
                [
                    'id' => $transaction->product_id,
                    'price' => $transaction->price,
                    'quantity' => $transaction->quantity,
                    'name' => $transaction->product_name,
                ],
                [
                    'id' => 'ADMIN-FEE',
                    'price' => $transaction->fee,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan Escrow',
                ]
            ],
            'customer_details' => [
                'first_name' => $transaction->buyer->name,
                'email' => $transaction->buyer->email,
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
