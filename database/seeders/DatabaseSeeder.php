<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'buyer@lootku.test',
        ], [
            'name' => 'Buyer Demo',
            'role' => 'buyer',
            'store_name' => null,
            'password' => 'password123',
        ]);

        User::query()->updateOrCreate([
            'email' => 'seller@lootku.test',
        ], [
            'name' => 'Seller Demo',
            'role' => 'seller',
            'store_name' => 'Demo Seller Store',
            'password' => 'password123',
        ]);

        User::query()->updateOrCreate([
            'email' => 'admin@lootku.test',
        ], [
            'name' => 'Admin Demo',
            'role' => 'admin',
            'store_name' => null,
            'password' => 'password123',
        ]);
    }
}
