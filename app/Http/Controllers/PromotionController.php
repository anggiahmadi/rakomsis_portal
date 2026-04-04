<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PromotionController extends Controller
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

        $query = $showDeleted ? Promotion::onlyTrashed() : Promotion::query();

        $promotions = $query
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            })
            ->when($request->promotion_rules, function ($q) use ($request) {
                $q->where('promotion_rules', $request->promotion_rules);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $products = Product::get();

        return view('admin.promotion', compact('promotions', 'products', 'showDeleted'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        $promotion->load(['products' => function ($query) {
            $query->select('products.id', 'products.code', 'products.name', 'products.price_per_location', 'products.price_per_user');
        }]);

        return response()->json([
            'success' => true,
            'promotion' => $promotion,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::get();
        return view('admin.promotion', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePromotionRequest $request)
    {
        DB::beginTransaction();

        $promotion = Promotion::create($request->validated());

        if ($request->included_products) {
            $promotion->products()->sync($request->included_products);
        }

        DB::commit();

        return Redirect::route('promotions.index')->with('success', 'Promotion created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        $promotion->load(['products' => function ($query) {
            $query->select('products.id', 'products.code', 'products.name', 'products.price_per_location', 'products.price_per_user');
        }]);

        return response()->json($promotion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        DB::beginTransaction();

        $promotion->update($request->validated());

        if ($request->included_products) {
            $promotion->products()->detach();

            $promotion->products()->sync($request->included_products);
        } else {
            $promotion->products()->detach();
        }

        DB::commit();

        return Redirect::route('promotions.index')->with('success', 'Promotion updated successfully.');
    }

    /**
     * Restore a soft-deleted promotion.
     */
    public function restore($id)
    {
        $promotion = Promotion::onlyTrashed()->findOrFail($id);
        $promotion->restore();

        return response()->json([
            'success' => true,
            'message' => 'Promotion restored successfully.'
        ]);
    }

    /**
     * Permanently delete a soft-deleted promotion.
     */
    public function permanentDelete($id)
    {
        $promotion = Promotion::onlyTrashed()->findOrFail($id);

        $isUsed = \App\Models\Subscription::withTrashed()->where('promotion_id', $promotion->id)->exists();

        if ($isUsed) {
            return response()->json([
                'success' => false,
                'message' => 'This promotion cannot be permanently deleted because it is referenced in subscriptions.'
            ], 422);
        }

        $promotion->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Promotion permanently deleted.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        // Check if promotion is used in subscriptions
        $isUsed = \App\Models\Subscription::where('promotion_id', $promotion->id)->exists();

        if ($isUsed) {
            return response()->json([
                'success' => false,
                'message' => 'This promotion cannot be deleted because it is currently used in active subscriptions.'
            ], 422);
        }

        $promotion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promotion deleted successfully.'
        ]);
    }
}
