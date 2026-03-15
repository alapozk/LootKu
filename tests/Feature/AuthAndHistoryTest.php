<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_as_buyer_and_is_redirected_to_transaction_history(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Bimo Buyer',
            'email' => 'bimo@example.com',
            'role' => 'buyer',
            'store_name' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'bimo@example.com',
            'role' => 'buyer',
        ]);
    }

    public function test_authenticated_user_can_view_own_transaction_history(): void
    {
        $buyer = User::factory()->create([
            'role' => 'buyer',
        ]);
        $seller = User::factory()->create([
            'role' => 'seller',
            'store_name' => 'Nova Store',
        ]);
        $otherBuyer = User::factory()->create([
            'role' => 'buyer',
        ]);

        Transaction::create([
            'reference' => 'ORD-1',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'product_name' => 'Steam Wallet 60K',
            'game_title' => 'Steam',
            'product_type' => 'Voucher',
            'status' => 'Selesai',
            'payment_method' => 'QRIS',
            'price' => 62500,
            'fee' => 3000,
            'total' => 62500,
            'ordered_at' => now(),
        ]);

        Transaction::create([
            'reference' => 'ORD-2',
            'buyer_id' => $otherBuyer->id,
            'seller_id' => $seller->id,
            'product_name' => 'Robux 800',
            'game_title' => 'Roblox',
            'product_type' => 'Top Up',
            'status' => 'Selesai',
            'payment_method' => 'Saldo',
            'price' => 123000,
            'fee' => 5000,
            'total' => 123000,
            'ordered_at' => now(),
        ]);

        $response = $this
            ->actingAs($buyer)
            ->get(route('transactions.index'));

        $response->assertOk();
        $response->assertSee('Steam Wallet 60K');
        $response->assertDontSee('Robux 800');
    }

    public function test_buyer_cannot_open_seller_dashboard(): void
    {
        $buyer = User::factory()->create([
            'role' => 'buyer',
        ]);

        $response = $this
            ->actingAs($buyer)
            ->get(route('seller.dashboard'));

        $response->assertRedirect(route('transactions.index'));
    }

    public function test_guest_is_redirected_to_login_when_opening_transaction_history(): void
    {
        $response = $this->get(route('transactions.index'));

        $response->assertRedirect(route('login'));
    }
}
