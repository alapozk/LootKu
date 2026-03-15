<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->string('game_title');
            $table->string('product_type');
            $table->string('status');
            $table->string('payment_method')->default('Saldo');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('game_user_id')->nullable();
            $table->text('buyer_note')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('fee')->default(0);
            $table->unsignedBigInteger('total');
            $table->timestamp('ordered_at');
            $table->timestamp('completed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['buyer_id', 'ordered_at']);
            $table->index(['seller_id', 'ordered_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
