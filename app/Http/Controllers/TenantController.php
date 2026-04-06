<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted');

        $query = Tenant::withCount(['customers', 'subscriptions', 'activeSubscriptions']);

        $query = $showDeleted ? $query->onlyTrashed() : $query->whereNull('deleted_at');

         if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%')
                  ->orWhere('domain', 'like', '%'.$request->q.'%')
                  ->orWhere('code', 'like', '%'.$request->q.'%');
        }

        $tenants = $query->orderBy('name')->paginate(20);

        return view('pages.tenant', compact('tenants', 'showDeleted'));
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
    public function store(StoreTenantRequest $request)
    {
        $tenant = Tenant::create($request->validated());

        // Attach the currently logged-in customer as owner of this tenant
        $customer = auth()->user()->customer;

        if ($customer) {
            $tenant->customers()->attach($customer->id, ['role' => 'owner']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tenant created successfully.',
            'tenant' => $tenant,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        return response()->json([
            'tenant' => $tenant->load('customers'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tenant deleted successfully.',
        ]);
    }

    /**
     * Restore a soft deleted tenant.
     */
    public function restore(Tenant $tenant)
    {
        $tenant->restore();

        return response()->json([
            'success' => true,
            'message' => 'Tenant restored successfully.',
        ]);
    }

    /**
     * Permanently delete a tenant.
     */
    public function permanentDelete(Tenant $tenant)
    {
        $tenant->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Tenant permanently deleted.',
        ]);
    }

    /**
     * Invite another customer to tenant by email.
     */
    public function inviteCustomer(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'nullable|string|in:owner,admin,user',
        ]);

        $authUser = auth()->user();

        if (! ($authUser instanceof User) || ! $authUser->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'Only customer users can invite other customers.',
            ], 403);
        }

        $authCustomer = $authUser->customer;

        if (! $authCustomer || ! $tenant->isOwner($authCustomer)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to invite customer to this tenant.',
            ], 403);
        }

        $targetCustomer = Customer::whereHas('user', function ($query) use ($validated) {
            $query->where('email', $validated['email']);
        })->first();

        if (! $targetCustomer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer with this email was not found.',
            ], 404);
        }

        $alreadyRelated = $tenant->customers()
            ->where('customers.id', $targetCustomer->id)
            ->exists();

        if ($alreadyRelated) {
            return response()->json([
                'success' => false,
                'message' => 'This customer is already related to the tenant.',
            ], 422);
        }

        $tenant->customers()->attach($targetCustomer->id, [
            'role' => $validated['role'] ?? 'user',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer invited successfully.',
        ]);
    }
}
