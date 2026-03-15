<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Support\MarketplaceUi;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(MarketplaceUi::availableTypes());
        $game = fake()->randomElement([
            'Mobile Legends',
            'Steam',
            'Roblox',
            'Valorant',
            'Genshin Impact',
            'Growtopia',
        ]);
        $name = fake()->words(3, true).' '.$type;

        return [
            'seller_id' => User::factory()->state([
                'role' => 'seller',
                'store_name' => fake()->company().' Store',
            ]),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(10, 99),
            'name' => $name,
            'game_title' => $game,
            'type' => $type,
            'price' => fake()->numberBetween(20000, 500000),
            'stock' => fake()->numberBetween(10, 500),
            'delivery_estimate' => fake()->randomElement(['Instan', '1-3 menit', '5-10 menit', '10-20 menit']),
            'region' => fake()->randomElement(['Indonesia', 'SEA', 'Global', 'Asia']),
            'highlight' => MarketplaceUi::highlightForType($type),
            'thumb' => MarketplaceUi::thumbFor($game, $type),
            'tone' => MarketplaceUi::toneForType($type),
            'description' => fake()->paragraph(),
            'tags' => [Str::slug($game), Str::slug($type)],
            'is_active' => true,
            'rating' => fake()->randomFloat(1, 4.6, 5.0),
            'sold_count' => fake()->numberBetween(0, 900),
        ];
    }
}
