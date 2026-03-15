<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\MarketplaceUi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = $request->user()->products()
            ->latest()
            ->get();

        return view('seller.products.index', [
            'pageTitle' => 'Kelola Listing | Lootku Market',
            'products' => $products,
            'availableTypes' => MarketplaceUi::availableTypes(),
        ]);
    }

    public function create(): View
    {
        return view('seller.products.form', [
            'pageTitle' => 'Buat Listing | Lootku Market',
            'product' => new Product([
                'type' => 'Top Up',
                'region' => 'Indonesia',
                'delivery_estimate' => 'Instan',
                'stock' => 1,
                'is_active' => true,
            ]),
            'availableTypes' => MarketplaceUi::availableTypes(),
            'formAction' => route('seller.products.store'),
            'formMethod' => 'POST',
            'formTitle' => 'Buat listing baru',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedProduct($request);
        $seller = $request->user();

        $seller->products()->create($this->payloadForProduct($validated));

        return redirect()
            ->route('seller.products.index')
            ->with('status', 'Listing baru berhasil dibuat.');
    }

    public function edit(Request $request, Product $product): View
    {
        abort_unless($product->seller_id === $request->user()->id, 403);

        return view('seller.products.form', [
            'pageTitle' => 'Edit Listing | Lootku Market',
            'product' => $product,
            'availableTypes' => MarketplaceUi::availableTypes(),
            'formAction' => route('seller.products.update', $product),
            'formMethod' => 'PUT',
            'formTitle' => 'Edit listing',
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->seller_id === $request->user()->id, 403);

        $validated = $this->validatedProduct($request, $product);
        $product->update($this->payloadForProduct($validated, $product));

        return redirect()
            ->route('seller.products.index')
            ->with('status', 'Listing berhasil diperbarui.');
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->seller_id === $request->user()->id, 403);

        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return back()->with('status', 'Status listing berhasil diubah.');
    }

    private function validatedProduct(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'game_title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(MarketplaceUi::availableTypes())],
            'price' => ['required', 'integer', 'min:1000'],
            'stock' => ['required', 'integer', 'min:0'],
            'delivery_estimate' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'tags' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function payloadForProduct(array $validated, ?Product $product = null): array
    {
        $slugBase = Str::slug($validated['name']);
        $slug = $slugBase;
        $counter = 2;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($product, fn ($builder) => $builder->whereKeyNot($product->id))
                ->exists()
        ) {
            $slug = $slugBase.'-'.$counter;
            $counter++;
        }

        return [
            'slug' => $slug,
            'name' => $validated['name'],
            'game_title' => $validated['game_title'],
            'type' => $validated['type'],
            'price' => (int) $validated['price'],
            'stock' => (int) $validated['stock'],
            'delivery_estimate' => $validated['delivery_estimate'],
            'region' => $validated['region'],
            'highlight' => MarketplaceUi::highlightForType($validated['type']),
            'thumb' => MarketplaceUi::thumbFor($validated['game_title'], $validated['type']),
            'tone' => MarketplaceUi::toneForType($validated['type']),
            'description' => $validated['description'],
            'tags' => MarketplaceUi::parseTags($validated['tags'] ?? ''),
            'is_active' => $validated['is_active'] ?? false,
            'rating' => $validated['rating'] ?? ($product?->rating ?? 4.8),
        ];
    }
}
