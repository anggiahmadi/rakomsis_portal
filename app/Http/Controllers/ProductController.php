<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
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
        $products = Product::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('code', 'like', '%' . $request->search . '%');
            })
            ->when($request->product_type, function ($query) use ($request) {
                $query->where('product_type', $request->product_type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.product', compact('products'));
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
        $product = Product::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'product' => $product
        ]);
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
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'product' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Check if product is used in subscriptions
        $isUsed = \App\Models\Subscription::where('product_id', $product->id)->exists();

        if ($isUsed) {
            return response()->json([
                'success' => false,
                'message' => 'This product cannot be deleted because it is currently used in active subscriptions.'
            ], 422);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }

    /**
     * Check if product can be deleted (not used in active subscriptions).
     */
    public function checkUsage(Product $product)
    {
        $this->authorize('delete', $product);

        $isUsed = \App\Models\Subscription::where('product_id', $product->id)->exists();

        return response()->json([
            'can_delete' => !$isUsed,
            'message' => $isUsed ? 'Product is currently used in subscriptions and cannot be deleted.' : 'Product can be safely deleted.'
        ]);
    }
}

