<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SellerStatsService;
use App\Support\MarketplaceUi;
use App\Support\TransactionFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function home(): View
    {
        $productCount = Cache::remember('home.productCount', 3600, fn() => Product::query()->where('is_active', true)->count());
        $sellerCount = Cache::remember('home.sellerCount', 3600, fn() => User::query()->where('role', 'seller')->count());
        $todayTransactions = Transaction::query()->whereDate('ordered_at', today())->count();

        $featuredProducts = Product::query()
            ->with('seller')
            ->where('is_active', true)
            ->orderByDesc('sold_count')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn (Product $product) => MarketplaceUi::productToCard($product))
            ->all();

        return view('buyer.home', [
            'pageTitle' => 'Lootku Market | Marketplace Item Game',
            'topTags' => MarketplaceUi::topTags(),
            'navigation' => MarketplaceUi::navigation(),
            'promoCards' => MarketplaceUi::promoCards(),
            'quickCategories' => MarketplaceUi::quickCategories(),
            'featuredProducts' => $featuredProducts,
            'featuredProductsCount' => $productCount,
            'trustSignals' => MarketplaceUi::trustSignals(),
            'marketStats' => [
                ['label' => 'Transaksi hari ini', 'value' => number_format($todayTransactions, 0, ',', '.')],
                ['label' => 'Seller aktif', 'value' => number_format($sellerCount, 0, ',', '.')],
                ['label' => 'Produk aktif', 'value' => number_format($productCount, 0, ',', '.')],
            ],
            'sellerPerks' => MarketplaceUi::sellerPerks(),
        ]);
    }

    public function catalog(Request $request): View
    {
        $query = trim($request->string('q')->toString());
        $type = $request->string('type')->toString();

        $products = Product::query()
            ->with('seller')
            ->where('is_active', true)
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($nested) use ($query) {
                    $nested->where('name', 'like', '%'.$query.'%')
                        ->orWhere('game_title', 'like', '%'.$query.'%')
                        ->orWhere('description', 'like', '%'.$query.'%');
                });
            })
            ->when($type !== '' && $type !== 'Semua', fn ($builder) => $builder->where('type', $type))
            ->orderByDesc('sold_count')
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Product $product) => MarketplaceUi::productToCard($product));

        $productTypes = Product::query()
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->values()
            ->all();

        return view('buyer.catalog', [
            'pageTitle' => 'Katalog Lootku Market',
            'topTags' => MarketplaceUi::topTags(),
            'navigation' => MarketplaceUi::navigation(),
            'products' => $products,
            'productTypes' => $productTypes,
            'selectedType' => $type,
            'searchQuery' => $query,
            'quickCategories' => MarketplaceUi::quickCategories(),
        ]);
    }

    public function sellerDashboard(Request $request, SellerStatsService $statsService): View
    {
        $data = $statsService->getDashboardData($request->user());
        $data['pageTitle'] = 'Dashboard Penjual | Lootku Market';

        return view('seller.dashboard', $data);
    }

}
