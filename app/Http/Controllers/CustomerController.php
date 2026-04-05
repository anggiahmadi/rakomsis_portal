<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private function canDeleteByAge(Customer $customer): bool
    {
        return $customer->created_at && $customer->created_at->lte(now()->subDays(30));
    }

    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted');

        $isAgent = $request->boolean('is_agent');

        $query = $showDeleted ? Customer::with('user', 'tenants')->onlyTrashed() : Customer::with('user', 'tenants');

        if ($isAgent) {
            $query->whereHas('user', function ($q) {
                $q->where('role', 'agent');
            });
        }

        $query = $query->with(['user', 'tenants']);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%');
        }

        $customers = $query->orderBy('name')->paginate(20);

        return view('admin.customer', compact('customers', 'showDeleted', 'isAgent'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer = Customer::with('user', 'tenants')->findOrFail($customer->id);

        $customer->user->agent;

        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        //
    }

    /**
     * Restore a soft-deleted customer.
     */
    public function restore($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();

        return response()->json([
            'success' => true,
            'message' => 'Customer restored successfully.'
        ]);
    }

    /**
     * Permanently delete a soft-deleted customer.
     */
    public function permanentDelete($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);

        if (! $this->canDeleteByAge($customer)) {
            return response()->json([
                'success' => false,
                'message' => 'Customer can only be deleted after 30 days from creation date.'
            ], 422);
        }

        $user = $customer->user;

        $isRelated = $customer->tenants()->exists();

        $isUsed = $customer->tenants()->whereHas('subscriptions')->exists();

        if ($isRelated || $isUsed) {
            return response()->json([
                'success' => false,
                'message' => 'This customer cannot be permanently deleted because it is referenced in subscriptions or bundles.'
            ], 422);
        }

        ($customer->forceDelete()) ? $user->forceDelete() : null;

        return response()->json([
            'success' => true,
            'message' => 'Customer permanently deleted.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if (! $this->canDeleteByAge($customer)) {
            return response()->json([
                'success' => false,
                'message' => 'Customer can only be deleted after 30 days from creation date.'
            ], 422);
        }

        // Check if customer is used in subscriptions
        $isRelated = $customer->tenants()->exists();

        $isUsed = $customer->tenants()->whereHas('subscriptions')->exists();

        if ($isRelated || $isUsed) {
            return response()->json([
                'success' => false,
                'message' => 'This customer cannot be deleted because it is currently used in active subscriptions.'
            ], 422);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.'
        ]);
    }
}
