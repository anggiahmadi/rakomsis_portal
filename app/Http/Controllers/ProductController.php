<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted');

        $query = $showDeleted ? Product::onlyTrashed() : Product::query();

        $products = $query
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            })
            ->when($request->product_type, function ($q) use ($request) {
                $q->where('product_type', $request->product_type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $singleProducts = Product::where('product_type', 'single')->get();

        return view('admin.product', compact('products', 'singleProducts', 'showDeleted'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.product');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();

        $product = Product::create($request->validated());

        // If it's a bundle, attach included products

        if ($request->product_type === 'bundle' && $request->included_products) {
            $product->includedProducts()->sync($request->included_products);
        }

        DB::commit();

        return Redirect::route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'product' => Product::with('includedProducts', 'mainProducts')->findOrFail($product->id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Load included products for bundles
        $product->load(['includedProducts' => function($query) {
            $query->select('products.id', 'products.code', 'products.name', 'products.price_per_location', 'products.price_per_user', 'products.product_type');
        }]);

        // Add descriptions for enums
        $productData = $product->toArray();
        $productData['billing_cycle_description'] = $product->billing_cycle->description();
        $productData['product_type_description'] = $product->product_type->description();

        // Add included products with descriptions
        $productData['included_products'] = $product->includedProducts->map(function($includedProduct) {
            return [
                'id' => $includedProduct->id,
                'code' => $includedProduct->code,
                'name' => $includedProduct->name,
                'price_per_location' => $includedProduct->price_per_location,
                'price_per_user' => $includedProduct->price_per_user,
                'product_type' => $includedProduct->product_type,
                'product_type_description' => $includedProduct->product_type->description(),
            ];
        });

        return response()->json($productData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::beginTransaction();

        $product->update($request->validated());

        // If it's a bundle, attach included products

        $product->includedProducts()->sync([]);

        if ($request->product_type === 'bundle' && $request->included_products) {
            $product->includedProducts()->sync($request->included_products);
        }

        DB::commit();

        return Redirect::route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return response()->json([
            'success' => true,
            'message' => 'Product restored successfully.'
        ]);
    }

    /**
     * Permanently delete a soft-deleted product.
     */
    public function permanentDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        $isUsed = \App\Models\Subscription::withTrashed()->where('product_id', $product->id)->exists();
        $isIncludedInBundle = DB::table('product_includes')->where('included_product_id', $product->id)->exists();

        if ($isUsed || $isIncludedInBundle) {
            return response()->json([
                'success' => false,
                'message' => 'This product cannot be permanently deleted because it is referenced in subscriptions or bundles.'
            ], 422);
        }

        $product->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Product permanently deleted.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Check if product is used in subscriptions
        $isUsed = \App\Models\Subscription::where('product_id', $product->id)->exists();

        $isIncludedInBundle = DB::table('product_includes')->where('included_product_id', $product->id)->exists();

        if ($isUsed || $isIncludedInBundle) {
            return response()->json([
                'success' => false,
                'message' => 'This product cannot be deleted because it is currently used in active subscriptions or included in a bundle.'
            ], 422);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }
}

