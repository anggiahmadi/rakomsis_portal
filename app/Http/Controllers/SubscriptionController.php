<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted');

        $productIds = $request->input('product_ids', []);

        $query = Subscription::withCount(['agent', 'tenant', 'products', 'promotion']);

        if (!Auth::user()->is_employee) {
            $query->whereHas('tenant', function ($q) {
                $q->whereHas('customers', function ($q2) {
                    $q2->where('user_id', Auth::id());
                });
            });
        }

        if (!empty($productIds)) {
            $query->whereHas('products', function ($q) use ($productIds) {
                $q->whereIn('products.id', $productIds);
            });
        }

        $query = $showDeleted ? $query->onlyTrashed() : $query->whereNull('deleted_at');

         if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%')
                  ->orWhere('domain', 'like', '%'.$request->q.'%')
                  ->orWhere('code', 'like', '%'.$request->q.'%');
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('pages.subscription', compact('subscriptions', 'showDeleted'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->middleware('customer');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        $this->middleware('customer');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        //
    }
}
