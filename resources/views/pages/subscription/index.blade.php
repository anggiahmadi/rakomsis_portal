@extends('components.layout.main')

@section('title', 'Subscription Management')

@section('content')
    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('subscriptions.index') }}" class="flex gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Subscriptions</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Search by name or email..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end gap-2">
                    <label class="flex items-center gap-2 pb-2 cursor-pointer select-none">
                        <input type="checkbox" name="show_deleted" value="1" {{ $showDeleted ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm font-medium text-gray-700">Show Deleted</span>
                    </label>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filter
                    </button>
                    @if (request('search') || $showDeleted)
                        <a href="{{ route('subscriptions.index') }}"
                            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Subscriptions Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $showDeleted ? 'Deleted Subscriptions' : 'Subscriptions' }} ({{ $subscriptions->total() }})
                </h3>
                @unless ($showDeleted && !Auth::user()->isAdmin())
                    <a href="{{ route('subscriptions.create') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                            </path>
                        </svg>
                        New Subscription
                    </a>
                @endunless
            </div>
            @if (!$isEmployee)
                <div class="px-6 py-3 bg-blue-50 border-b border-blue-100 text-sm text-blue-800">
                    You are viewing subscriptions related to your tenant(s) only.
                </div>
            @endif

            @if ($subscriptions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Code</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Account Information</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Product</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Period</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment Status</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($subscriptions as $subscription)
                                <tr class="{{ $subscription->trashed() ? 'bg-red-50 opacity-75' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @php
                                            $subscriptionStatusValue = is_object($subscription->subscription_status)
                                                ? $subscription->subscription_status->value
                                                : (string) $subscription->subscription_status;

                                            $subscriptionStatusLabel = ucfirst(
                                                str_replace('_', ' ', $subscriptionStatusValue ?: 'unknown'),
                                            );

                                            $subscriptionStatusClasses = match ($subscriptionStatusValue) {
                                                'active' => 'bg-green-100 text-green-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                                'expired' => 'bg-gray-100 text-gray-700',
                                                default => 'bg-amber-100 text-amber-700',
                                            };
                                        @endphp

                                        <div class="flex space-x-2">
                                            @if ($subscription->trashed())
                                                <button type="button"
                                                    onclick="restoreSubscription({{ $subscription->id }}, @js($subscription->tenant->name ?? ($subscription->customer_name ?? 'Subscription')))"
                                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                    Restore
                                                </button>
                                                <button type="button"
                                                    onclick="permanentDeleteSubscription({{ $subscription->id }}, @js($subscription->tenant->name ?? ($subscription->customer_name ?? 'Subscription')))"
                                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Delete Permanently
                                                </button>
                                            @else
                                                <button type="button" onclick="viewSubscription({{ $subscription->id }})"
                                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                    View
                                                </button>
                                                @if (
                                                    !$subscription->is_trial &&
                                                        $subscription->subscription_status === \App\Enums\SubscriptionStatus::Active &&
                                                        $subscription->payment_status === \App\Enums\PaymentStatus::Completed)
                                                    <button type="button"
                                                        onclick="cancelSubscription({{ $subscription->id }}, @js($subscription->tenant->name ?? ($subscription->customer_name ?? 'Subscription')))"
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                        Cancel Subscription
                                                    </button>
                                                @endif

                                                @if (!$subscription->is_trial && $subscription->payment_status !== \App\Enums\PaymentStatus::Completed)
                                                    <button type="button"
                                                        onclick="paySubscription({{ $subscription->id }}, @js($subscription->tenant->name ?? ($subscription->customer_name ?? 'Subscription')))"
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                        Pay Subscription
                                                    </button>
                                                @endif

                                                @if (!$subscription->is_trial && $subscription->subscription_status === \App\Enums\SubscriptionStatus::Expired)
                                                    <a href="{{ route('subscriptions.create', ['renew_from' => $subscription->id]) }}"
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                        Renew Subscription
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $subscriptionStatusClasses }}">
                                                Status: {{ $subscriptionStatusLabel }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $subscription->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        <div class="max-w-xs truncate {{ $subscription->trashed() ? 'line-through text-gray-400' : '' }}"
                                            title="{{ $subscription->tenant->name ?? '-' }}">
                                            Tenant : {{ $subscription->tenant->name ?? '-' }}
                                        </div>
                                        @if ($subscription->trashed())
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 mt-1">
                                                Deleted {{ $subscription->deleted_at->format('M d, Y') }}
                                            </span>
                                        @endif

                                        Customer Email: {{ $subscription->customer_email ?? '-' }}

                                        <br>

                                        Domain : {{ $subscription->tenant->domain ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $subscription->products->pluck('name')->join(', ') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        {{ $subscription->billing_cycle->label() }} <br>
                                        {{ $subscription->start_date->format('M d, Y') }} -
                                        {{ $subscription->end_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($subscription->is_trial)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">
                                                Trial
                                            </span>
                                        @elseif ($subscription->total == 0)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                                Free
                                            </span>
                                        @else
                                            {{ number_format($subscription->total, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $subscription->payment_status->label() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $subscription->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ $subscriptions->firstItem() }} to {{ $subscriptions->lastItem() }} of
                            {{ $subscriptions->total() }} results
                        </div>
                        <div class="flex-1 flex justify-end">
                            {{ $subscriptions->appends(request()->query())->links('vendor.pagination.tailwind') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-5.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No subscriptions found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if (request('search'))
                            No subscriptions match your search criteria.
                        @else
                            Get started by creating your first subscription.
                        @endif
                    </p>
                    @if (request('search'))
                        <div class="mt-6">
                            <a href="{{ route('subscriptions.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Clear Search
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Renew Subscription Modal -->
    <div id="subscription-form-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-0 hidden">
        <div class="bg-white w-full h-full max-w-none shadow-2xl overflow-hidden border-0 rounded-none flex flex-col">
            <div class="px-5 py-3 bg-green-700 text-white flex items-center justify-between">
                <h3 id="subscription-form-modal-title" class="text-base sm:text-lg font-bold">Renew Subscription</h3>
                <button id="close-subscription-form-modal" class="text-white hover:text-slate-200">✕</button>
            </div>

            <form id="subscription-renew-form" method="POST" action=""
                class="p-6 space-y-5 overflow-y-auto flex-1">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="renew-tenant-id" class="block text-sm font-medium text-gray-700 mb-1">Tenant &
                            Domain</label>
                        <select id="renew-tenant-id" name="tenant_id" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Select tenant</option>
                            @foreach ($availableTenants ?? collect() as $tenantOption)
                                <option value="{{ $tenantOption->id }}">
                                    {{ $tenantOption->name }} ({{ $tenantOption->domain ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="renew-products" class="block text-sm font-medium text-gray-700 mb-1">Products</label>
                        <input id="renew-products-search" type="text" placeholder="Search products..."
                            class="w-full mb-2 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <select id="renew-products" name="product_ids[]" multiple required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 h-32">
                            @foreach ($availableProducts ?? collect() as $productOption)
                                <option value="{{ $productOption->id }}">
                                    {{ $productOption->name }} - Rp
                                    {{ number_format($productOption->price_per_user, 0, ',', '.') }} per user /
                                    {{ number_format($productOption->price_per_location, 0, ',', '.') }} per location
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Cmd/Ctrl to select multiple products.</p>
                    </div>

                    <div>
                        <label for="renew-price-type" class="block text-sm font-medium text-gray-700 mb-1">Price
                            Type</label>
                        <select id="renew-price-type" name="price_type"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="per_user">Per User</option>
                            <option value="per_location">Per Location</option>
                        </select>
                    </div>
                    <div>
                        <label for="renew-billing-cycle" class="block text-sm font-medium text-gray-700 mb-1">Billing
                            Cycle</label>
                        <select id="renew-billing-cycle" name="billing_cycle" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>

                    <div>
                        <label for="renew-length-of-term" class="block text-sm font-medium text-gray-700 mb-1">Length of
                            Term</label>
                        <input id="renew-length-of-term" name="length_of_term" type="number" min="1"
                            step="1" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="renew-quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input id="renew-quantity" name="quantity" type="number" min="1" step="1"
                            required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label for="renew-start-date" class="block text-sm font-medium text-gray-700 mb-1">Start
                            Date</label>
                        <input id="renew-start-date" name="start_date" type="date" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="renew-end-date" class="block text-sm font-medium text-gray-700 mb-1">End Date
                            (Auto)</label>
                        <input id="renew-end-date" name="end_date" type="date" readonly
                            class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="renew-reference-code" class="block text-sm font-medium text-gray-700 mb-1">Reference
                            Code</label>
                        <div class="flex gap-2">
                            <input id="renew-reference-code" name="reference_code" type="text"
                                placeholder="Enter promotion/reference code"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <button type="button" id="renew-check-reference"
                                class="px-3 py-2 rounded-md bg-blue-100 text-slate-700 text-sm font-medium hover:bg-blue-200">
                                Check
                            </button>
                        </div>
                        <p id="renew-reference-status" class="mt-1 text-xs text-gray-500">No code applied.</p>
                    </div>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-lg">
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-slate-200">
                            <tr>
                                <td class="px-4 py-2 font-medium text-slate-700 bg-slate-50">Base Price</td>
                                <td id="renew-calc-base" class="px-4 py-2 text-right text-slate-800">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium text-slate-700 bg-slate-50">Tax</td>
                                <td id="renew-calc-tax" class="px-4 py-2 text-right text-slate-800">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium text-slate-700 bg-slate-50">Discount</td>
                                <td id="renew-calc-discount" class="px-4 py-2 text-right text-slate-800">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold text-slate-900 bg-slate-100">Total</td>
                                <td id="renew-calc-total" class="px-4 py-2 text-right font-semibold text-slate-900">Rp 0
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
                    <button type="button" id="close-subscription-form-modal-cta"
                        class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200">
                        Cancel
                    </button>
                    <button id="subscription-form-submit-btn" type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Subscription Modal -->
    <div id="subscription-view-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
        <div
            class="bg-white rounded-xl w-full max-w-xl sm:max-w-2xl lg:max-w-3xl shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                <h3 id="subscription-view-modal-title" class="text-base sm:text-lg font-bold">Subscription Details</h3>
                <button id="close-subscription-view-modal" class="text-white hover:text-slate-200">✕</button>
            </div>

            <div class="flex border-b border-blue-100 bg-blue-50">
                <button id="subscription-view-details-tab" onclick="setSubscriptionViewTab('details')"
                    class="w-1/2 px-4 py-3 text-sm sm:text-base font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none">
                    Basic Information
                </button>
                <button id="subscription-view-tenant-tab" onclick="setSubscriptionViewTab('tenant')"
                    class="w-1/2 px-4 py-3 text-sm sm:text-base font-medium text-blue-700 bg-gray-100 hover:bg-blue-50 focus:outline-none">
                    Tenant Information
                </button>
            </div>

            <div id="subscription-view-content-details" class="p-6">
                <dl class="grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-2 text-sm text-gray-700">
                    <div>
                        <dt class="font-semibold">Code</dt>
                        <dd id="subscription-view-code">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Customer Name</dt>
                        <dd id="subscription-view-customer-name">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Customer Email</dt>
                        <dd id="subscription-view-customer-email">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Period</dt>
                        <dd id="subscription-view-period">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Product(s)</dt>
                        <dd id="subscription-view-products">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Status</dt>
                        <dd id="subscription-view-status">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Payment Status</dt>
                        <dd id="subscription-view-payment-status">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Total</dt>
                        <dd id="subscription-view-total">-</dd>
                    </div>
                </dl>
            </div>

            <div id="subscription-view-content-tenant" class="p-6 hidden">
                <dl class="grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-2 text-sm text-gray-700">
                    <div>
                        <dt class="font-semibold">Tenant Name</dt>
                        <dd id="subscription-view-tenant-name">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Tenant Code</dt>
                        <dd id="subscription-view-tenant-code">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Domain</dt>
                        <dd id="subscription-view-tenant-domain">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Business Type</dt>
                        <dd id="subscription-view-tenant-business-type">-</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-semibold">Address</dt>
                        <dd id="subscription-view-tenant-address">-</dd>
                    </div>
                </dl>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 text-right">
                <button id="close-subscription-view-modal-cta" onclick="closeModal('subscription-view-modal')"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Close</button>
            </div>
        </div>
    </div>

    @php
        $subscriptionFormProductsData = ($availableProducts ?? collect())
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price_per_user' => (float) ($product->price_per_user ?? 0),
                    'price_per_location' => (float) ($product->price_per_location ?? 0),
                    'tax_percentage' => (float) ($product->tax_percentage ?? 0),
                ];
            })
            ->values()
            ->all();

        $subscriptionFormPromotionsData = ($availablePromotions ?? collect())
            ->map(function ($promotion) {
                return [
                    'id' => $promotion->id,
                    'code' => $promotion->code,
                    'name' => $promotion->name,
                    'promotion_rules' => is_object($promotion->promotion_rules)
                        ? $promotion->promotion_rules->value
                        : $promotion->promotion_rules,
                    'billing_cycle' => is_object($promotion->billing_cycle)
                        ? $promotion->billing_cycle->value
                        : $promotion->billing_cycle,
                    'specific_length_of_term' => $promotion->specific_length_of_term,
                    'discount_type' => is_object($promotion->discount_type)
                        ? $promotion->discount_type->value
                        : $promotion->discount_type,
                    'discount_value' => (float) ($promotion->discount_value ?? 0),
                    'has_specific_product' => (bool) $promotion->has_specific_product,
                    'product_ids' => $promotion->products
                        ->pluck('id')
                        ->map(function ($id) {
                            return (int) $id;
                        })
                        ->values()
                        ->all(),
                    'start_date' => optional($promotion->start_date)->format('Y-m-d'),
                    'end_date' => optional($promotion->end_date)->format('Y-m-d'),
                ];
            })
            ->values()
            ->all();
    @endphp

    <script>
        const subscriptionFormProducts = @json($subscriptionFormProductsData);
        const subscriptionFormPromotions = @json($subscriptionFormPromotionsData);

        function setSubscriptionViewTab(tab) {
            const detailsTab = document.getElementById('subscription-view-details-tab');
            const tenantTab = document.getElementById('subscription-view-tenant-tab');
            const detailsContent = document.getElementById('subscription-view-content-details');
            const tenantContent = document.getElementById('subscription-view-content-tenant');

            if (tab === 'tenant') {
                detailsTab.classList.remove('bg-white', 'text-blue-700');
                detailsTab.classList.add('bg-gray-100', 'text-gray-700');
                tenantTab.classList.add('bg-white', 'text-blue-700');
                tenantTab.classList.remove('bg-gray-100', 'text-gray-700');
                detailsContent.classList.add('hidden');
                tenantContent.classList.remove('hidden');
            } else {
                detailsTab.classList.add('bg-white', 'text-blue-700');
                detailsTab.classList.remove('bg-gray-100', 'text-gray-700');
                tenantTab.classList.remove('bg-white', 'text-blue-700');
                tenantTab.classList.add('bg-gray-100', 'text-gray-700');
                detailsContent.classList.remove('hidden');
                tenantContent.classList.add('hidden');
            }
        }

        function openSubscriptionViewModal() {
            document.getElementById('subscription-view-modal').classList.remove('hidden');
            setSubscriptionViewTab('details');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function normalizeEnumValue(rawValue) {
            if (rawValue && typeof rawValue === 'object') {
                return rawValue.value || rawValue.name || '';
            }

            return rawValue || '';
        }

        function toInputDate(dateValue) {
            if (!dateValue) {
                return '';
            }

            const parsed = new Date(dateValue);
            if (Number.isNaN(parsed.getTime())) {
                return '';
            }

            const year = parsed.getFullYear();
            const month = String(parsed.getMonth() + 1).padStart(2, '0');
            const day = String(parsed.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        function addDays(dateValue, daysToAdd) {
            const parsed = new Date(dateValue);
            if (Number.isNaN(parsed.getTime())) {
                return '';
            }

            parsed.setDate(parsed.getDate() + daysToAdd);
            return toInputDate(parsed);
        }

        function formatCurrency(amountValue) {
            const parsed = Number(amountValue);
            if (Number.isNaN(parsed)) {
                return 'Rp 0';
            }

            return `Rp ${parsed.toLocaleString('id-ID')}`;
        }

        function getSelectedProductIds() {
            const select = document.getElementById('renew-products');
            return Array.from(select.selectedOptions).map(option => Number(option.value)).filter(Boolean);
        }

        function filterProductOptions() {
            const searchInput = document.getElementById('renew-products-search');
            const select = document.getElementById('renew-products');
            const keyword = String(searchInput.value || '').trim().toLowerCase();

            Array.from(select.options).forEach(option => {
                const label = String(option.textContent || '').toLowerCase();
                const matches = keyword === '' || label.includes(keyword);
                option.hidden = !matches;
            });
        }

        function findPromotionByCode(rawCode) {
            const code = String(rawCode || '').trim().toLowerCase();
            if (!code) {
                return null;
            }

            return subscriptionFormPromotions.find(item => String(item.code || '').toLowerCase() === code) || null;
        }

        function evaluateReferencePromotion() {
            const statusNode = document.getElementById('renew-reference-status');
            const code = document.getElementById('renew-reference-code').value;
            const promotion = findPromotionByCode(code);

            if (!promotion) {
                statusNode.textContent = code ? 'Reference code not found or inactive.' : 'No code applied.';
                statusNode.className = code ? 'mt-1 text-xs text-red-600' : 'mt-1 text-xs text-gray-500';
                return null;
            }

            const billingCycle = document.getElementById('renew-billing-cycle').value;
            const lengthOfTerm = Number(document.getElementById('renew-length-of-term').value || 1);
            const selectedProductIds = getSelectedProductIds();

            if (promotion.promotion_rules === 'specific_length_of_term') {
                if (promotion.billing_cycle !== billingCycle || Number(promotion.specific_length_of_term || 0) !==
                    lengthOfTerm) {
                    statusNode.textContent = 'Code found, but billing cycle/term does not match promotion rule.';
                    statusNode.className = 'mt-1 text-xs text-amber-600';
                    return null;
                }
            }

            if (promotion.has_specific_product) {
                const matchesProduct = selectedProductIds.some(id => Array.isArray(promotion.product_ids) && promotion
                    .product_ids.includes(id));
                if (!matchesProduct) {
                    statusNode.textContent = 'Code found, but selected products are not eligible.';
                    statusNode.className = 'mt-1 text-xs text-amber-600';
                    return null;
                }
            }

            statusNode.textContent = `Code valid: ${promotion.code} (${promotion.name})`;
            statusNode.className = 'mt-1 text-xs text-green-700';
            return promotion;
        }

        function calculateRenewEndDate() {
            const startDate = document.getElementById('renew-start-date').value;
            const lengthOfTerm = Number(document.getElementById('renew-length-of-term').value || 1);
            const billingCycle = document.getElementById('renew-billing-cycle').value;
            const endDateInput = document.getElementById('renew-end-date');

            if (!startDate || !lengthOfTerm || lengthOfTerm < 1) {
                endDateInput.value = '';
                return;
            }

            const baseDate = new Date(startDate);
            if (Number.isNaN(baseDate.getTime())) {
                endDateInput.value = '';
                return;
            }

            if (billingCycle === 'yearly') {
                baseDate.setFullYear(baseDate.getFullYear() + lengthOfTerm);
            } else {
                baseDate.setMonth(baseDate.getMonth() + lengthOfTerm);
            }

            baseDate.setDate(baseDate.getDate() - 1);
            endDateInput.value = toInputDate(baseDate);
        }

        function recalculateRenewSummary() {
            const productIds = getSelectedProductIds();
            const priceType = document.getElementById('renew-price-type').value;
            const quantity = Number(document.getElementById('renew-quantity').value || 1);
            const lengthOfTerm = Number(document.getElementById('renew-length-of-term').value || 1);

            const selectedProducts = subscriptionFormProducts.filter(item => productIds.includes(Number(item.id)));

            const unitPrice = selectedProducts.reduce((sum, product) => {
                const base = priceType === 'per_location' ? Number(product.price_per_location || 0) : Number(product
                    .price_per_user || 0);
                return sum + base;
            }, 0);

            const unitTax = selectedProducts.reduce((sum, product) => {
                const base = priceType === 'per_location' ? Number(product.price_per_location || 0) : Number(product
                    .price_per_user || 0);
                const taxPct = Number(product.tax_percentage || 0);
                return sum + (base * (taxPct / 100));
            }, 0);

            const basePrice = unitPrice * quantity * lengthOfTerm;
            const tax = unitTax * quantity * lengthOfTerm;

            const promotion = evaluateReferencePromotion();
            let discount = 0;

            if (promotion) {
                const discountValue = Number(promotion.discount_value || 0);
                const discountType = String(promotion.discount_type || '').toLowerCase();
                discount = discountType === 'percentage' ? ((basePrice + tax) * (discountValue / 100)) : discountValue;
            }

            const total = Math.max(0, (basePrice + tax) - discount);

            document.getElementById('renew-calc-base').textContent = formatCurrency(basePrice);
            document.getElementById('renew-calc-tax').textContent = formatCurrency(tax);
            document.getElementById('renew-calc-discount').textContent = formatCurrency(discount);
            document.getElementById('renew-calc-total').textContent = formatCurrency(total);
        }

        function setMultiSelectValues(selectId, values) {
            const targetValues = new Set((values || []).map(item => String(item)));
            const options = document.getElementById(selectId).options;

            for (let i = 0; i < options.length; i += 1) {
                options[i].selected = targetValues.has(options[i].value);
            }
        }

        function resetSubscriptionFormInputs() {
            document.getElementById('renew-tenant-id').value = '';
            setMultiSelectValues('renew-products', []);
            document.getElementById('renew-products-search').value = '';
            filterProductOptions();
            document.getElementById('renew-price-type').value = 'per_user';
            document.getElementById('renew-billing-cycle').value = 'monthly';
            document.getElementById('renew-length-of-term').value = 1;
            document.getElementById('renew-quantity').value = 1;
            document.getElementById('renew-start-date').value = '';
            document.getElementById('renew-end-date').value = '';
            document.getElementById('renew-reference-code').value = '';
            document.getElementById('renew-reference-status').textContent = 'No code applied.';
            document.getElementById('renew-reference-status').className = 'mt-1 text-xs text-gray-500';
            recalculateRenewSummary();
        }

        function openSubscriptionFormModal() {
            document.getElementById('subscription-form-modal').classList.remove('hidden');
        }

        function openNewSubscriptionModal() {
            const form = document.getElementById('subscription-renew-form');
            form.action = '{{ route('subscriptions.store') }}';
            form.dataset.mode = 'new';

            document.getElementById('subscription-form-modal-title').textContent = 'New Subscription';

            resetSubscriptionFormInputs();
            openSubscriptionFormModal();
        }

        function renderSubscriptionView(data) {
            const subscription = data.subscription || {};
            const tenant = subscription.tenant || {};

            const formatDate = (dateValue) => {
                if (!dateValue) {
                    return '-';
                }

                const parsed = new Date(dateValue);
                if (Number.isNaN(parsed.getTime())) {
                    return dateValue;
                }

                return parsed.toLocaleDateString(undefined, {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                });
            };

            const formatStatus = (statusValue) => {
                if (!statusValue) {
                    return '-';
                }

                return String(statusValue)
                    .replaceAll('_', ' ')
                    .replace(/\b\w/g, char => char.toUpperCase());
            };

            const formatCurrency = (amountValue) => {
                const parsed = Number(amountValue);
                if (Number.isNaN(parsed)) {
                    return '-';
                }

                return parsed.toLocaleString('id-ID');
            };

            const products = Array.isArray(subscription.products) ?
                subscription.products.map(item => item?.name).filter(Boolean).join(', ') :
                '-';

            document.getElementById('subscription-view-code').textContent = subscription.code || '-';
            document.getElementById('subscription-view-customer-name').textContent = subscription.customer_name || '-';
            document.getElementById('subscription-view-customer-email').textContent = subscription.customer_email || '-';
            document.getElementById('subscription-view-products').textContent = products || '-';
            document.getElementById('subscription-view-period').textContent =
                `${formatDate(subscription.start_date)} - ${formatDate(subscription.end_date)}`;
            document.getElementById('subscription-view-status').textContent = formatStatus(subscription
                .subscription_status);
            document.getElementById('subscription-view-payment-status').textContent = formatStatus(subscription
                .payment_status);
            document.getElementById('subscription-view-total').textContent = subscription.is_trial ? 'Trial' :
                `Rp ${formatCurrency(subscription.total)}`;

            document.getElementById('subscription-view-tenant-name').textContent = tenant.name || '-';
            document.getElementById('subscription-view-tenant-code').textContent = tenant.code || '-';
            document.getElementById('subscription-view-tenant-domain').textContent = tenant.domain || '-';
            document.getElementById('subscription-view-tenant-business-type').textContent = tenant.business_type || '-';
            document.getElementById('subscription-view-tenant-address').textContent = tenant.address || '-';

            openSubscriptionViewModal();
        }

        function viewSubscription(subscriptionId) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('{{ url('subscriptions') }}/' + subscriptionId, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => Promise.reject(data));
                    }
                    return response.json();
                })
                .then(data => {
                    renderSubscriptionView(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load subscription data.');
                });
        }

        function renewSubscription(subscriptionId, subscriptionName) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('{{ url('subscriptions') }}/' + subscriptionId, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => Promise.reject(data));
                    }
                    return response.json();
                })
                .then(data => {
                    const subscription = data.subscription || {};
                    const tenant = subscription.tenant || {};

                    document.getElementById('subscription-form-modal-title').textContent =
                        `Renew Subscription - ${subscriptionName}`;

                    const form = document.getElementById('subscription-renew-form');
                    form.action = `{{ url('subscriptions') }}/${subscriptionId}/renew`;
                    form.dataset.mode = 'renew';

                    document.getElementById('renew-tenant-id').value = tenant.id ? String(tenant.id) : '';
                    setMultiSelectValues(
                        'renew-products',
                        Array.isArray(subscription.products) ? subscription.products.map(item => item.id) : []
                    );

                    document.getElementById('renew-price-type').value = subscription.price_type || 'per_user';
                    document.getElementById('renew-billing-cycle').value = normalizeEnumValue(subscription
                            .billing_cycle) ||
                        'monthly';
                    document.getElementById('renew-length-of-term').value = subscription.length_of_term || 1;
                    document.getElementById('renew-quantity').value = subscription.quantity || 1;
                    document.getElementById('renew-reference-code').value = subscription.promotion?.code || '';

                    const startAfterEndDate = addDays(subscription.end_date, 1);
                    document.getElementById('renew-start-date').value = startAfterEndDate;

                    calculateRenewEndDate();
                    recalculateRenewSummary();
                    openSubscriptionFormModal();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load subscription data for renewal.');
                });
        }

        async function cancelSubscription(subscriptionId, subscriptionName) {
            const confirmed = window.confirm(
                `Cancel subscription "${subscriptionName}"? This action cannot be undone.`
            );

            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('subscriptions') }}/${subscriptionId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'cancel'
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw result;
                }

                alert(result.message || 'Subscription canceled successfully.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to cancel subscription.');
            }
        }

        async function restoreSubscription(subscriptionId, subscriptionName) {
            const confirmed = window.confirm(`Restore subscription "${subscriptionName}"?`);
            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('subscriptions') }}/${subscriptionId}/restore`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    throw result;
                }

                alert(result.message || 'Subscription restored successfully.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to restore subscription.');
            }
        }

        async function permanentDeleteSubscription(subscriptionId, subscriptionName) {
            const confirmed = window.confirm(
                `Permanently delete subscription "${subscriptionName}"? This cannot be undone and will remove all data.`
            );
            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('subscriptions') }}/${subscriptionId}/permanent-delete`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    throw result;
                }

                alert(result.message || 'Subscription permanently deleted.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to permanently delete subscription.');
            }
        }

        document.getElementById('close-subscription-view-modal').addEventListener('click', function() {
            closeModal('subscription-view-modal');
        });

        document.getElementById('close-subscription-form-modal').addEventListener('click', function() {
            closeModal('subscription-form-modal');
        });

        document.getElementById('close-subscription-form-modal-cta').addEventListener('click', function() {
            closeModal('subscription-form-modal');
        });

        document.getElementById('renew-start-date').addEventListener('change', function() {
            calculateRenewEndDate();
            recalculateRenewSummary();
        });
        document.getElementById('renew-length-of-term').addEventListener('input', function() {
            calculateRenewEndDate();
            recalculateRenewSummary();
        });
        document.getElementById('renew-billing-cycle').addEventListener('change', function() {
            calculateRenewEndDate();
            recalculateRenewSummary();
        });
        document.getElementById('renew-quantity').addEventListener('input', recalculateRenewSummary);
        document.getElementById('renew-price-type').addEventListener('change', recalculateRenewSummary);
        document.getElementById('renew-products').addEventListener('change', recalculateRenewSummary);
        document.getElementById('renew-products-search').addEventListener('input', filterProductOptions);
        document.getElementById('renew-reference-code').addEventListener('input', recalculateRenewSummary);
        document.getElementById('renew-check-reference').addEventListener('click', recalculateRenewSummary);

        document.getElementById('close-subscription-view-modal-cta').addEventListener('click', function() {
            closeModal('subscription-view-modal');
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal('subscription-view-modal');
                closeModal('subscription-form-modal');
            }
        });
    </script>

@endsection
