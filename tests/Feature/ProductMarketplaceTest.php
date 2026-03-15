<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_create_listing(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'store_name' => 'Nova Loot',
        ]);

        $response = $this
            ->actingAs($seller)
            ->post(route('seller.products.store'), [
                'name' => 'Steam Wallet 120K',
                'game_title' => 'Steam',
                'type' => 'Voucher',
                'price' => 125000,
                'stock' => 40,
                'delivery_estimate' => 'Instan',
                'region' => 'Indonesia',
                'description' => 'Voucher Steam Wallet untuk buyer yang butuh isi saldo cepat.',
                'tags' => 'steam, wallet, voucher',
                'rating' => 4.9,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('seller.products.index'));
        $this->assertDatabaseHas('products', [
            'seller_id' => $seller->id,
            'name' => 'Steam Wallet 120K',
            'type' => 'Voucher',
            'is_active' => 1,
        ]);
    }

    public function test_buyer_can_checkout_product_and_stock_is_reduced(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'store_name' => 'Fast Topup',
        ]);
        $buyer = User::factory()->create([
            'role' => 'buyer',
        ]);
        $product = Product::factory()->for($seller, 'seller')->create([
            'name' => 'Mobile Legends Diamond 86',
            'slug' => 'mobile-legends-diamond-86',
            'price' => 20000,
            'stock' => 10,
            'type' => 'Top Up',
        ]);
        \Illuminate\Support\Facades\Queue::fake();

        $response = $this
            ->actingAs($buyer)
            ->post(route('checkout.store', $product), [
                'quantity' => 2,
                'payment_method' => 'Saldo',
                'game_user_id' => 'ML-889900',
                'buyer_note' => 'Top up malam ini.',
            ]);

        $response->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'product_name' => 'Mobile Legends Diamond 86',
            'quantity' => 2,
            'status' => 'Diproses', // Job difake dengan Queue::fake() sehingga prosesnya akan tertahan pada "Diproses"
        ]);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\ProcessAutoFulfillmentJob::class);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
            'sold_count' => $product->sold_count + 2,
        ]);
    }

    public function test_seller_can_update_transaction_status(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'store_name' => 'Vault Seller',
        ]);
        $buyer = User::factory()->create([
            'role' => 'buyer',
        ]);
        $product = Product::factory()->for($seller, 'seller')->create();
        $transaction = Transaction::create([
            'reference' => 'ORD-STATUS-1',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'game_title' => $product->game_title,
            'product_type' => $product->type,
            'status' => 'Diproses',
            'payment_method' => 'Saldo',
            'quantity' => 1,
            'game_user_id' => 'UID-1',
            'buyer_note' => 'Please fast.',
            'price' => $product->price,
            'fee' => 1000,
            'total' => $product->price,
            'ordered_at' => now(),
        ]);

        $response = $this
            ->actingAs($seller)
            ->patch(route('seller.transactions.update-status', $transaction), [
                'status' => 'Selesai',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'Selesai',
        ]);
    }

    public function test_guest_is_redirected_to_login_when_opening_checkout(): void
    {
        $product = Product::factory()->create([
            'slug' => 'guest-checkout-product',
        ]);

        $response = $this->get(route('checkout.show', $product));

        $response->assertRedirect(route('login'));
    }

    public function test_seller_dashboard_loads_without_dummy_data(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'store_name' => 'Fresh Store',
        ]);

        $response = $this
            ->actingAs($seller)
            ->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertSee('Fresh Store');
        $response->assertSee('Belum ada order masuk');
    }
}
