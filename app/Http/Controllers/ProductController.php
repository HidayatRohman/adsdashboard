<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->with(['brand','variations'])->orderBy('name')->paginate(10)->withQueryString();

        $brands = Brand::orderBy('name')->get();

        return Inertia::render('products/Index', [
            'products' => $products,
            'brands' => $brands,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'nullable|integer',
            'brand_id' => 'nullable|exists:brands,id',
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|integer',
            'variations.*.name' => 'nullable|string',
            'variations.*.price' => 'nullable|integer',
        ]);

        $variations = $data['variations'] ?? [];
        unset($data['variations']);

        $product = Product::create($data);

        // sync variations
        foreach ($variations as $v) {
            if (isset($v['id'])) {
                $pv = ProductVariation::find($v['id']);
                if ($pv) $pv->update(['name' => $v['name'] ?? null, 'price' => $v['price'] ?? 0]);
            } else {
                ProductVariation::create(['product_id' => $product->id, 'name' => $v['name'] ?? null, 'price' => $v['price'] ?? 0]);
            }
        }

        return redirect()->back()->with('success', 'Product created.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'nullable|integer',
            'brand_id' => 'nullable|exists:brands,id',
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|integer',
            'variations.*.name' => 'nullable|string',
            'variations.*.price' => 'nullable|integer',
        ]);

        $variations = $data['variations'] ?? [];
        unset($data['variations']);

        $product->update($data);

        // sync variations: update/create; delete those not in payload
        $incomingIds = [];
        foreach ($variations as $v) {
            if (isset($v['id'])) {
                $pv = ProductVariation::find($v['id']);
                if ($pv) {
                    $pv->update(['name' => $v['name'] ?? null, 'price' => $v['price'] ?? 0]);
                    $incomingIds[] = $pv->id;
                }
            } else {
                $new = ProductVariation::create(['product_id' => $product->id, 'name' => $v['name'] ?? null, 'price' => $v['price'] ?? 0]);
                $incomingIds[] = $new->id;
            }
        }

        // delete variations not present
        ProductVariation::where('product_id', $product->id)->whereNotIn('id', $incomingIds)->delete();

        return redirect()->back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted.');
    }
}
