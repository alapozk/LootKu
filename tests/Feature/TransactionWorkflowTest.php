<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_cancel_pending_transaction_and_stock_is_restored(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'store_name' => 'Cancel Store',
        ]);
        $buyer = User::factory()->create([
            'role' => 'buyer',
        ]);
        $product = Product::factory()->for($seller, 'seller')->create([
            'stock' => 3,
            'sold_count' => 2,
        ]);
        $transaction = Transaction::create([
            'reference' => 'ORD-CANCEL-1',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'game_title' => $product->game_title,
            'product_type' => $product->type,
            'status' => 'Menunggu Pembayaran',
            'payment_method' => 'QRIS',
            'quantity' => 2,
            'game_user_id' => 'UID-CANCEL',
            'price' => $product->price * 2,
            'fee' => 1500,
            'total' => $product->price * 2,
            'ordered_at' => now(),
        ]);

        $response = $this
            ->actingAs($buyer)
            ->patch(route('transactions.buyer-action', $transaction), [
                'action' => 'cancel',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'Dibatalkan',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
            'sold_count' => 0,
        ]);
    }

    public function test_buyer_can_complete_processing_transaction(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'store_name' => 'Done Store',
        ]);
        $buyer = User::factory()->create([
            'role' => 'buyer',
        ]);
        $product = Product::factory()->for($seller, 'seller')->create();
        $transaction = Transaction::create([
            'reference' => 'ORD-DONE-1',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'game_title' => $product->game_title,
            'product_type' => $product->type,
            'status' => 'Diproses',
            'payment_method' => 'Saldo',
            'quantity' => 1,
            'game_user_id' => 'UID-DONE',
            'price' => $product->price,
            'fee' => 1500,
            'total' => $product->price,
            'ordered_at' => now(),
        ]);

        $response = $this
            ->actingAs($buyer)
            ->patch(route('transactions.buyer-action', $transaction), [
                'action' => 'complete',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'Selesai',
        ]);
    }

    public function test_seller_can_send_message_and_transaction_moves_to_chat_status(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'store_name' => 'Chat Store',
        ]);
        $buyer = User::factory()->create([
            'role' => 'buyer',
        ]);
        $product = Product::factory()->for($seller, 'seller')->create();
        $transaction = Transaction::create([
            'reference' => 'ORD-CHAT-1',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'game_title' => $product->game_title,
            'product_type' => $product->type,
            'status' => 'Diproses',
            'payment_method' => 'Saldo',
            'quantity' => 1,
            'game_user_id' => 'UID-CHAT',
            'price' => $product->price,
            'fee' => 1500,
            'total' => $product->price,
            'ordered_at' => now(),
        ]);

        $response = $this
            ->actingAs($seller)
            ->post(route('transactions.messages.store', $transaction), [
                'message' => 'Mohon cek inbox game Anda, item sedang dikirim.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transaction_messages', [
            'transaction_id' => $transaction->id,
            'user_id' => $seller->id,
        ]);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'Perlu cek chat',
        ]);
    }

    public function test_admin_can_view_dashboard_and_transaction_detail(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $seller = User::factory()->create([
            'role' => 'seller',
            'store_name' => 'Monitor Store',
        ]);
        $buyer = User::factory()->create([
            'role' => 'buyer',
        ]);
        $product = Product::factory()->for($seller, 'seller')->create([
            'is_active' => true,
        ]);
        $transaction = Transaction::create([
            'reference' => 'ORD-ADMIN-1',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'game_title' => $product->game_title,
            'product_type' => $product->type,
            'status' => 'Diproses',
            'payment_method' => 'Saldo',
            'quantity' => 1,
            'game_user_id' => 'UID-ADMIN',
            'price' => $product->price,
            'fee' => 1500,
            'total' => $product->price,
            'ordered_at' => now(),
        ]);

        $dashboardResponse = $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'));

        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Dashboard Admin');
        $dashboardResponse->assertSee('ORD-ADMIN-1');

        $detailResponse = $this
            ->actingAs($admin)
            ->get(route('transactions.show', $transaction));

        $detailResponse->assertOk();
        $detailResponse->assertSee('ORD-ADMIN-1');
        $detailResponse->assertSee('Monitor Store');
    }
}
