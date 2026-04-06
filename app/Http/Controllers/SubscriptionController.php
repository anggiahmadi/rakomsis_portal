<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\ProductType;
use App\Enums\SubscriptionStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Subscription;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted');

        $query = Subscription::with([
            'tenant' => function ($q) {
                $q->withTrashed();
            },
            'products',
            'promotion',
            'agent',
        ]);

        $isEmployee = (bool) Auth::user()->is_employee;

        if (! $isEmployee) {
            $query->whereHas('tenant', function ($q) {
                $q->whereHas('customers', function ($q2) {
                    $q2->where('user_id', Auth::id());
                });
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_email', 'like', '%' . $search . '%')
                    ->orWhereHas('tenant', function ($tenantQuery) use ($search) {
                        $tenantQuery->withTrashed()
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('domain', 'like', '%' . $search . '%');
                    });
            });
        }

        $query = $showDeleted ? $query->onlyTrashed() : $query->withoutTrashed();

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);

        $availableTenants = $isEmployee
            ? Tenant::query()->orderBy('name')->get(['id', 'code', 'name', 'domain'])
            : Auth::user()->customer?->tenants()->orderBy('name')->get(['tenants.id', 'tenants.code', 'tenants.name', 'tenants.domain']);

        $availableProducts = Product::query()->where('product_type', ProductType::Bundle->value)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'price_per_user', 'price_per_location', 'tax_percentage']);

        $availablePromotions = Promotion::query()
            ->with('products:id')
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'promotion_rules',
                'billing_cycle',
                'specific_length_of_term',
                'discount_type',
                'discount_value',
                'has_specific_product',
                'start_date',
                'end_date',
            ]);

        return view('pages.subscription.index', compact(
            'subscriptions',
            'showDeleted',
            'isEmployee',
            'availableTenants',
            'availableProducts',
            'availablePromotions'
        ));
    }

    public function create(Request $request)
    {
        $isEmployee = (bool) Auth::user()->is_employee;

        $availableTenants = $isEmployee
            ? Tenant::query()->orderBy('name')->get(['id', 'code', 'name', 'domain'])
            : Auth::user()->customer?->tenants()->orderBy('name')->get(['tenants.id', 'tenants.code', 'tenants.name', 'tenants.domain']);

        $availableProducts = Product::query()->where('product_type', ProductType::Bundle->value)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'price_per_user', 'price_per_location', 'tax_percentage', 'billing_cycle']);

        $availablePromotions = Promotion::query()
            ->with('products:id')
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'promotion_rules',
                'billing_cycle',
                'specific_length_of_term',
                'discount_type',
                'discount_value',
                'has_specific_product',
                'start_date',
                'end_date',
            ]);

        $renewSubscription = null;
        $renewFromId = (int) $request->query('renew_from', 0);

        if ($renewFromId > 0) {
            $renewSubscription = Subscription::with([
                'tenant' => function ($q) {
                    $q->withTrashed();
                },
                'products',
                'promotion',
            ])->findOrFail($renewFromId);

            if (! Auth::user()->is_employee) {
                $isRelated = $renewSubscription->tenant
                    ? $renewSubscription->tenant->customers()->where('user_id', Auth::id())->exists()
                    : false;

                if (! $isRelated) {
                    return Redirect::route('subscriptions.index')->with('error', 'Unauthorized access to renewal operation.');
                }
            }
        }

        return view('pages.subscription.form', compact(
            'isEmployee',
            'availableTenants',
            'availableProducts',
            'availablePromotions',
            'renewSubscription'
        ));
    }

    /**
     * Display the specified resource for modal payload.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load([
            'tenant' => function ($q) {
                $q->withTrashed();
            },
            'products',
            'promotion',
            'agent',
        ]);

        if (!Auth::user()->is_employee) {
            $isRelated = $subscription->tenant
                ? $subscription->tenant->customers()->where('user_id', Auth::id())->exists()
                : false;

            if (! $isRelated) {
                abort(403, 'Unauthorized access to subscription details.');
            }
        }

        $relatedSubscriptions = Subscription::with([
            'tenant' => function ($q) {
                $q->withTrashed();
            },
        ])
            ->where('tenant_id', $subscription->tenant_id)
            ->where('id', '!=', $subscription->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'subscription' => $subscription,
            'related_subscriptions' => $relatedSubscriptions,
        ]);
    }

    /**
     * Update the specified resource in storage (for canceling subscription).
     */
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'action' => ['required', 'in:cancel'],
        ]);

        $subscription->subscription_status = SubscriptionStatus::Cancelled->value;

        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Subscription canceled successfully.',
        ]);
    }

    /**
     * Store a new subscription from modal form.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['exists:products,id'],
            'price_type' => ['required', 'in:per_user,per_location'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'quantity' => ['required', 'integer', 'min:1'],
            'length_of_term' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'reference_code' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $this->userCanAccessTenant((int) $validated['tenant_id'])) {
            return Redirect::back()->withInput()->withErrors([
                'tenant_id' => 'Selected tenant does not belong to your account.',
            ]);
        }

        $createdSubscription = $this->createSubscriptionFromRequest(
            $validated,
            (int) $validated['tenant_id'],
            null,
            null
        );

        return Redirect::route('subscriptions.index')
            ->with('success', 'Subscription created successfully. Code: ' . $createdSubscription->code);
    }

    /**
     * Create a new renewal subscription from an existing subscription.
     */
    public function renew(Request $request, Subscription $subscription)
    {
        $subscription->load([
            'tenant' => function ($q) {
                $q->withTrashed();
            },
            'products',
        ]);

        if (! Auth::user()->is_employee) {
            $isRelated = $subscription->tenant
                ? $subscription->tenant->customers()->where('user_id', Auth::id())->exists()
                : false;

            if (! $isRelated) {
                return Redirect::route('subscriptions.index')->with('error', 'Unauthorized access to renewal operation.');
            }
        }

        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['exists:products,id'],
            'price_type' => ['required', 'in:per_user,per_location'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'quantity' => ['required', 'integer', 'min:1'],
            'length_of_term' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'reference_code' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $this->userCanAccessTenant((int) $validated['tenant_id'])) {
            return Redirect::back()->withInput()->withErrors([
                'tenant_id' => 'Selected tenant does not belong to your account.',
            ]);
        }

        $renewedSubscription = $this->createSubscriptionFromRequest(
            $validated,
            (int) $validated['tenant_id'],
            $subscription->agent_id,
            false
        );

        return Redirect::route('subscriptions.index')
            ->with('success', 'Subscription renewed successfully. New code: ' . $renewedSubscription->code);
    }

    /**
     * Verify if the user can access the specified tenant.
     */
    private function userCanAccessTenant(int $tenantId): bool
    {
        if ((bool) Auth::user()->is_employee) {
            return Tenant::query()->where('id', $tenantId)->exists();
        }

        return Auth::user()->customer?->tenants()->where('tenants.id', $tenantId)->exists() ?? false;
    }

    /**
     * Create a new subscription from the request data.
     */
    private function createSubscriptionFromRequest(array $validated, int $tenantId, ?int $agentId = null, ?bool $isTrial = false): Subscription
    {
        $user = Auth::user();
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = (clone $startDate);

        if ($validated['billing_cycle'] === 'yearly') {
            $endDate->addYears((int) $validated['length_of_term']);
        } else {
            $endDate->addMonths((int) $validated['length_of_term']);
        }

        $endDate->subDay();

        $products = Product::query()->whereIn('id', $validated['product_ids'])->get();

        $priceType = $validated['price_type'];
        $quantity = (int) $validated['quantity'];
        $lengthOfTerm = (int) $validated['length_of_term'];

        $unitPrice = $products->sum(function (Product $product) use ($priceType) {
            return (float) ($priceType === 'per_location' ? $product->price_per_location : $product->price_per_user);
        });

        $unitTax = $products->sum(function (Product $product) use ($priceType) {
            $base = (float) ($priceType === 'per_location' ? $product->price_per_location : $product->price_per_user);
            $taxPercentage = (float) ($product->tax_percentage ?? 0);

            return $base * ($taxPercentage / 100);
        });

        $subtotal = $unitPrice * $quantity * $lengthOfTerm;
        $tax = $unitTax * $quantity * $lengthOfTerm;

        $promotion = null;
        $discountType = null;
        $discountValue = 0.0;
        $discountAmount = 0.0;

        if (! empty($validated['reference_code'])) {
            $promotion = $this->resolveAvailablePromotion(
                (string) $validated['reference_code'],
                $validated['billing_cycle'],
                $lengthOfTerm,
                array_map('intval', $validated['product_ids'])
            );

            if (! $promotion) {
                throw ValidationException::withMessages([
                    'reference_code' => 'Reference code is not available for current subscription setup.',
                ]);
            }

            $discountTypeRaw = is_object($promotion->discount_type)
                ? $promotion->discount_type->value
                : (string) $promotion->discount_type;

            $discountType = $discountTypeRaw === 'fixed_amount' ? 'fixed' : $discountTypeRaw;
            $discountValue = (float) ($promotion->discount_value ?? 0);

            $discountAmount = $discountType === 'percentage'
                ? (($subtotal + $tax) * ($discountValue / 100))
                : $discountValue;
        }

        $total = max(0, ($subtotal + $tax) - $discountAmount);

        return DB::transaction(function () use (
            $tenantId,
            $agentId,
            $promotion,
            $isTrial,
            $user,
            $validated,
            $quantity,
            $lengthOfTerm,
            $startDate,
            $endDate,
            $unitPrice,
            $tax,
            $discountType,
            $discountValue,
            $subtotal,
            $total
        ) {
            $newSubscription = Subscription::create([
                'tenant_id' => $tenantId,
                'agent_id' => $agentId,
                'promotion_id' => $promotion?->id,
                'code' => 'SUB-' . strtoupper(substr(uniqid(), -8)),
                'is_trial' => (bool) ($isTrial ?? false),
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'price_type' => $validated['price_type'],
                'billing_cycle' => $validated['billing_cycle'],
                'quantity' => $quantity,
                'length_of_term' => $lengthOfTerm,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'tax_percentage' => 0,
                'price' => $unitPrice,
                'tax' => $tax,
                'discount_type' => $discountType,
                'discount' => $discountValue,
                'subtotal' => $subtotal,
                'total' => $total,
                'agent_commission' => 0,
            ]);

            $newSubscription->products()->sync($validated['product_ids']);

            return $newSubscription;
        });
    }

     /**
     * Resolve the available promotion based on the reference code, billing cycle, length of term, and product IDs.
     */
    private function resolveAvailablePromotion(string $referenceCode, string $billingCycle, int $lengthOfTerm, array $productIds): ?Promotion
    {
        $code = trim($referenceCode);

        if ($code === '') {
            return null;
        }

        $promotion = Promotion::query()
            ->with('products:id')
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->first();

        if (! $promotion) {
            return null;
        }

        $rules = is_object($promotion->promotion_rules)
            ? $promotion->promotion_rules->value
            : (string) $promotion->promotion_rules;

        $promotionBillingCycle = is_object($promotion->billing_cycle)
            ? $promotion->billing_cycle->value
            : (string) $promotion->billing_cycle;

        if ($rules === 'specific_length_of_term') {
            if ($promotionBillingCycle !== $billingCycle) {
                return null;
            }

            if ((int) ($promotion->specific_length_of_term ?? 0) !== $lengthOfTerm) {
                return null;
            }
        }

        if ((bool) $promotion->has_specific_product) {
            $allowedProductIds = $promotion->products->pluck('id')->map(fn ($id) => (int) $id)->all();
            $matchesAny = count(array_intersect($allowedProductIds, $productIds)) > 0;

            if (! $matchesAny) {
                return null;
            }
        }

        return $promotion;
    }
}
