<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'slug',
        'name',
        'game_title',
        'type',
        'price',
        'stock',
        'delivery_estimate',
        'region',
        'highlight',
        'thumb',
        'tone',
        'description',
        'tags',
        'is_active',
        'rating',
        'sold_count',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_active' => 'boolean',
            'rating' => 'decimal:1',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
