<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Enums\ProductType;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get customer data
        $customer = Customer::where('user_id', $user->id)->first();
        $customerTenants = $customer ? $customer->tenants()->orderBy('name')->get() : collect();
        $trialProducts = Product::query()
            ->where('product_type', ProductType::Bundle->value)
            ->orderBy('name')
            ->get();

        // Get tenants count
        $tenants_count = 0;
        if ($customer) {
            $tenants_count = $customer->tenants()->count();
        }

        // Get unpaid subscriptions count
        $total_subscription = 0;

        if ($customer) {
            $tenant_ids = $customer->tenants()->pluck('id');
            $total_subscription = Subscription::whereIn('tenant_id', $tenant_ids)->count();
        }

        // Get agent data
        $agent = Agent::where('user_id', $user->id)->first();

        return view('dashboard.index', [
            'user' => $user,
            'customer' => $customer,
            'customerTenants' => $customerTenants,
            'trialProducts' => $trialProducts,
            'tenants_count' => $tenants_count,
            'total_subscription' => $total_subscription,
            'agent' => $agent,
        ]);
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();

        return view('dashboard.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return Redirect::route('dashboard.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        return view('dashboard.settings');
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return Redirect::route('dashboard.settings')->with('success', 'Password updated successfully!');
    }

    /**
     * Show free trial page
     */
    public function freeTrial()
    {
        $user = Auth::user();

        $customer = Customer::where('user_id', $user->id)->first();

        $customerTenants = $customer ? $customer->tenants()->orderBy('name')->get() : collect();

        $trialProducts = Product::query()
            ->where('product_type', ProductType::Bundle->value)
            ->orderBy('name')
            ->get();

        return view('dashboard.free_trial', compact(
            'customer',
            'customerTenants',
            'trialProducts'
        ));
    }


    /**
     * Show the form for creating a new start trial.
     */
    public function startTrial(Request $request)
    {
        $user = Auth::user();

        $customer = Customer::where('user_id', $user->id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'trial_target' => ['required', 'in:tenants,subscription'],
            'product_id' => ['required', 'exists:products,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'tenant_mode' => ['required', 'in:existing,new'],
            'tenant_id' => ['nullable', 'required_if:tenant_mode,existing', 'exists:tenants,id'],
            'tenant_name' => ['nullable', 'required_if:tenant_mode,new', 'string', 'max:255'],
            'tenant_domain' => ['nullable', 'required_if:tenant_mode,new', 'string', 'max:63', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'tenant_address' => ['nullable', 'string', 'max:255'],
            'tenant_business_type' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return Redirect::route('dashboard.free-trial')
                ->withErrors($validator, 'trial')
                ->withInput();
        }

        $validated = $validator->validated();

        if ($validated['tenant_mode'] === 'new') {
            $domainPrefix = strtolower(trim((string) $validated['tenant_domain']));
            $fullDomain = $domainPrefix . '.rakomsis.com';

            $domainExists = Tenant::query()->where('domain', $fullDomain)->exists();

            if ($domainExists) {
                return Redirect::route('dashboard.free-trial')
                    ->withErrors(['tenant_domain' => 'This domain is already in use. Please choose another subdomain.'], 'trial')
                    ->withInput();
            }

            $validated['tenant_domain'] = $fullDomain;
        }

        $existingTenantIds = $customer->tenants()->pluck('tenants.id');

        if ($validated['tenant_mode'] === 'existing') {
            if (! $existingTenantIds->contains((int) $validated['tenant_id'])) {
                return Redirect::route('dashboard.free-trial')
                    ->withErrors(['tenant_id' => 'Selected tenant does not belong to your account.'], 'trial')
                    ->withInput();
            }
        }

        $product = Product::findOrFail($validated['product_id']);

        if ((is_object($product->product_type) ? $product->product_type->value : $product->product_type) !== ProductType::Bundle->value) {
            return Redirect::route('dashboard.free-trial')
                ->withErrors(['product_id' => 'Only bundle products can be selected for a free trial.'], 'trial')
                ->withInput();
        }

        $startDate = Carbon::parse($validated['start_date']);

        $endDate = (clone $startDate)->addMonth()->subDay();

        DB::transaction(function () use ($validated, $customer, $user, $product, $startDate, $endDate) {
            if ($validated['tenant_mode'] === 'new') {
                $tenant = Tenant::create([
                    'code' => 'TEN-' . strtoupper(substr(uniqid(), -6)),
                    'domain' => $validated['tenant_domain'],
                    'name' => $validated['tenant_name'],
                    'address' => $validated['tenant_address'] ?? null,
                    'business_type' => $validated['tenant_business_type'] ?? null,
                ]);

                $customer->tenants()->attach($tenant->id, ['role' => 'owner']);
            } else {
                $tenant = Tenant::findOrFail($validated['tenant_id']);
            }

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'agent_id' => null,
                'promotion_id' => null,
                'code' => 'SUB-' . strtoupper(substr(uniqid(), -8)),
                'is_trial' => true,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? '',
                'price_type' => 'per_user',
                'billing_cycle' => method_exists($product->billing_cycle, 'value') ? $product->billing_cycle->value : 'monthly',
                'quantity' => 1,
                'length_of_term' => 1,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'tax_percentage' => 0,
                'price' => 0,
                'tax' => 0,
                'discount_type' => 'percentage',
                'discount' => 0,
                'subtotal' => 0,
                'total' => 0,
                'agent_commission' => 0,
                'payment_status' => PaymentStatus::Completed->value,
                'subscription_status' => SubscriptionStatus::Active->value,
            ]);

            $subscription->products()->attach($product->id);
        });

        return Redirect::route('subscriptions.index')->with('success', 'Free trial has been created successfully.');
    }
}
