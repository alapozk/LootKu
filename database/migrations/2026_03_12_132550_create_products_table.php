<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('game_title');
            $table->string('type');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('stock');
            $table->string('delivery_estimate');
            $table->string('region');
            $table->string('highlight')->nullable();
            $table->string('thumb', 8)->nullable();
            $table->string('tone')->nullable();
            $table->text('description');
            $table->json('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('rating', 2, 1)->default(4.8);
            $table->unsignedInteger('sold_count')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index(['seller_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
