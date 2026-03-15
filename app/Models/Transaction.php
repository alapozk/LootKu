<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'buyer_id',
        'seller_id',
        'product_id',
        'product_name',
        'game_title',
        'product_type',
        'status',
        'snap_token',
        'payment_method',
        'quantity',
        'game_user_id',
        'buyer_note',
        'price',
        'fee',
        'total',
        'ordered_at',
        'completed_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'completed_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TransactionMessage::class);
    }

    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Review::class);
    }
}
