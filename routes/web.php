<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\SellerTransactionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionHistoryController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'home'])->name('home');
Route::get('/katalog', [MarketplaceController::class, 'catalog'])->name('catalog');
Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::middleware(['guest', 'throttle:5,1'])->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->name('login.store');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/riwayat-transaksi', [TransactionHistoryController::class, 'index'])->name('transactions.index');
    Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transaksi/{transaction}/review', [TransactionController::class, 'storeReview'])->name('transactions.review.store');
    Route::patch('/transaksi/{transaction}/aksi', [TransactionController::class, 'buyerAction'])->name('transactions.buyer-action');
    Route::post('/transaksi/{transaction}/pesan', [TransactionController::class, 'storeMessage'])->name('transactions.messages.store');
    Route::get('/checkout/{product:slug}', [ProductController::class, 'showCheckout'])->name('checkout.show');
    Route::post('/checkout/{product:slug}', [ProductController::class, 'checkout'])->name('checkout.store');
});

Route::middleware(['auth', 'role:seller'])->group(function () {
    Route::get('/seller/dashboard', [MarketplaceController::class, 'sellerDashboard'])->name('seller.dashboard');
    Route::get('/seller/produk', [SellerProductController::class, 'index'])->name('seller.products.index');
    Route::get('/seller/produk/buat', [SellerProductController::class, 'create'])->name('seller.products.create');
    Route::post('/seller/produk', [SellerProductController::class, 'store'])->name('seller.products.store');
    Route::get('/seller/produk/{product}/edit', [SellerProductController::class, 'edit'])->name('seller.products.edit');
    Route::put('/seller/produk/{product}', [SellerProductController::class, 'update'])->name('seller.products.update');
    Route::patch('/seller/produk/{product}/toggle', [SellerProductController::class, 'toggle'])->name('seller.products.toggle');
    Route::patch('/seller/transaksi/{transaction}/status', [SellerTransactionController::class, 'updateStatus'])->name('seller.transactions.update-status');
    Route::post('/seller/withdrawals', [WithdrawalController::class, 'store'])->name('seller.withdrawals.store');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::patch('/admin/withdrawals/{withdrawal}', [AdminController::class, 'updateWithdrawal'])->name('admin.withdrawals.update');
    Route::patch('/admin/disputes/{transaction}', [AdminController::class, 'resolveDispute'])->name('admin.disputes.resolve');
});
