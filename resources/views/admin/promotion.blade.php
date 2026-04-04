@extends('components.layout.main')

@section('title', 'Promotion Management')

@section('content')
    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('promotions.index') }}" class="flex gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Promotions</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Search by name or code..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="w-48">
                    <label for="has_specific_promotion" class="block text-sm font-medium text-gray-700 mb-1">Promotion
                        Rules</label>
                    <select name="promotion_rules" id="promotion_rules"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="all" {{ request('promotion_rules') === 'all' ? 'selected' : '' }}>All</option>
                        <option value="new_customers"
                            {{ request('promotion_rules') === 'new_customers' ? 'selected' : '' }}>
                            New Customers</option>
                        <option value="specific_length_of_term"
                            {{ request('promotion_rules') === 'specific_length_of_term' ? 'selected' : '' }}>Specific Length
                            of Term</option>
                    </select>
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
                    @if (request('search') || request('promotion_rules') || $showDeleted)
                        <a href="{{ route('promotions.index') }}"
                            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Promotions Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $showDeleted ? 'Deleted Promotions' : 'Promotions' }} ({{ $promotions->total() }})
                </h3>
                @unless ($showDeleted)
                    <button type="button" onclick="openPromotionFormModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                            </path>
                        </svg>
                        Add New Promotion
                    </button>
                @endunless
            </div>

            @if ($promotions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Discount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rules</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Specific Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($promotions as $promotion)
                                <tr class="{{ $promotion->trashed() ? 'bg-red-50 opacity-75' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if ($promotion->trashed())
                                                <button type="button"
                                                    onclick="restorePromotion({{ $promotion->id }}, @js($promotion->name))"
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
                                                    onclick="permanentDeletePromotion({{ $promotion->id }}, @js($promotion->name))"
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
                                                <button type="button" onclick="viewPromotion({{ $promotion->id }})"
                                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
                                                <button type="button" onclick="editPromotion({{ $promotion->id }})"
                                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                    Edit
                                                </button>
                                                <button type="button"
                                                    onclick="deletePromotion({{ $promotion->id }}, @js($promotion->name))"
                                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $promotion->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="max-w-xs truncate {{ $promotion->trashed() ? 'line-through text-gray-400' : '' }}"
                                            title="{{ $promotion->name }}">
                                            {{ $promotion->name }}
                                        </div>
                                        @if ($promotion->trashed())
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 mt-1">
                                                Deleted {{ $promotion->deleted_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $discountType = is_object($promotion->discount_type)
                                                ? $promotion->discount_type->value
                                                : (string) $promotion->discount_type;
                                            $discountLabel =
                                                $discountType === 'fixed_amount'
                                                    ? 'IDR ' . number_format($promotion->discount_value)
                                                    : rtrim(
                                                            rtrim(number_format($promotion->discount_value, 2), '0'),
                                                            '.',
                                                        ) . '%';
                                        @endphp
                                        {{ $discountLabel }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $rulesValue = is_object($promotion->promotion_rules)
                                                ? $promotion->promotion_rules->value
                                                : (string) $promotion->promotion_rules;
                                        @endphp
                                        {{ ucwords(str_replace('_', ' ', $rulesValue)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ optional($promotion->start_date)->format('d M Y') }} -
                                        {{ optional($promotion->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $promotion->has_specific_product ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $promotion->has_specific_product ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $promotion->created_at->format('M d, Y H:i') }}
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
                            Showing {{ $promotions->firstItem() }} to {{ $promotions->lastItem() }} of
                            {{ $promotions->total() }} results
                        </div>
                        <div class="flex-1 flex justify-end">
                            {{ $promotions->appends(request()->query())->links('vendor.pagination.tailwind') }}
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No promotions found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if (request('search'))
                            No promotions match your search criteria.
                        @else
                            Get started by creating your first promotion.
                        @endif
                    </p>
                    @if (request('search'))
                        <div class="mt-6">
                            <a href="{{ route('promotions.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Clear Search
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- View Promotion Modal -->
    <div id="promotion-view-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
        <div
            class="bg-white rounded-xl w-full max-w-xl sm:max-w-2xl lg:max-w-3xl shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                <h3 id="promotion-view-modal-title" class="text-base sm:text-lg font-bold">Promotion Details</h3>
                <button id="close-promotion-view-modal" class="text-white hover:text-slate-200">✕</button>
            </div>

            <div class="p-6">
                <dl class="grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-2 text-sm text-gray-700">
                    <div>
                        <dt class="font-semibold">Code</dt>
                        <dd id="promotion-view-code">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Name</dt>
                        <dd id="promotion-view-name">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Rules</dt>
                        <dd id="promotion-view-type">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Discount</dt>
                        <dd id="promotion-view-billing-cycle">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Period</dt>
                        <dd id="promotion-view-price-location">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Billing Cycle</dt>
                        <dd id="promotion-view-price-user">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Specific Product</dt>
                        <dd id="promotion-view-tax">-</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-semibold">Description</dt>
                        <dd id="promotion-view-description">-</dd>
                    </div>
                </dl>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 text-right">
                <button id="close-promotion-view-modal-cta" onclick="closeModal('promotion-view-modal')"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Close</button>
            </div>
        </div>
    </div>

    <!-- Promotion Form Modal -->
    <div id="promotion-form-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
        <div
            class="bg-white rounded-xl w-full max-w-md sm:max-w-xl lg:max-w-2xl shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                <h3 id="promotion-form-modal-title" class="text-base sm:text-lg font-bold">Create Promotion</h3>
                <button id="close-promotion-form-modal" class="text-white hover:text-slate-200">✕</button>
            </div>

            <form id="promotion-form" class="p-6 space-y-4" method="POST" action="{{ route('promotions.store') }}">
                @csrf
                <input type="hidden" name="_method" id="promotion-form-method" value="POST">

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Code</label>
                        <input type="text" name="code" id="promotion-code"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Name</label>
                        <input type="text" name="name" id="promotion-name"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-blue-900">Description</label>
                    <textarea name="description" id="promotion-description" rows="3"
                        class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2"></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Start Date</label>
                        <input type="date" name="start_date" id="promotion-start-date" required
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">End Date</label>
                        <input type="date" name="end_date" id="promotion-end-date" required
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Promotion Rules</label>
                        <select name="promotion_rules" id="promotion-rules"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                            <option value="all">All</option>
                            <option value="new_customers">New Customers</option>
                            <option value="specific_length_of_term">Specific Length of Term</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Billing Cycle</label>
                        <select name="billing_cycle" id="promotion-billing-cycle"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div id="promotion-specific-length-wrapper">
                        <label class="block text-sm font-semibold text-blue-900">Specific Length of Term (months)</label>
                        <input type="number" min="1" step="1" name="specific_length_of_term"
                            id="promotion-specific-length"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Apply to Specific Products</label>
                        <div class="mt-2 flex items-center gap-2">
                            <input type="hidden" name="has_specific_product" value="0">
                            <input type="checkbox" id="promotion-has-specific-product" name="has_specific_product"
                                value="1" onchange="toggleIncludedProductsContainer(this.checked)"
                                class="w-4 h-4 rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                            <label for="promotion-has-specific-product" class="text-sm text-gray-700">Enable product
                                selection</label>
                        </div>
                        <p id="promotion-specific-product-hint" class="mt-1 text-xs text-gray-500">
                            Select a rule other than "All" to enable product selection.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Discount Type</label>
                        <select name="discount_type" id="promotion-discount-type"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                            <option value="percentage">Percentage</option>
                            <option value="amount">Amount</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Discount Value</label>
                        <input type="number" step="0.01" min="0" name="discount_value"
                            id="promotion-discount-value"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                    </div>
                </div>

                <div class="mt-4 hidden" id="product-form-included-products-container">
                    <label class="block text-sm font-semibold text-blue-900">Included Products</label>
                    <input type="text" id="product-form-included-search"
                        placeholder="Search included products by code or name..."
                        class="mt-2 block w-full border border-blue-200 rounded-md px-3 py-2 text-sm">
                    <select id="product-form-included-products" name="included_products[]" multiple
                        class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2 min-h-44">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->code }} - {{ $product->name }}
                                ({{ $product->price_per_location > 0 ? 'IDR ' . number_format($product->price_per_location) . ' per location' : '' }}
                                {{ $product->price_per_user > 0 ? 'IDR ' . number_format($product->price_per_user) . ' per user' : '' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500">
                        Hold Cmd (Mac) or Ctrl (Windows) to select multiple items.
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-blue-100">
                    <button type="button" id="close-promotion-form-modal-cta"
                        class="px-4 py-2 rounded-md border border-blue-300 text-blue-700 hover:bg-blue-50">Cancel</button>
                    <button type="submit" id="promotion-form-submit-button"
                        class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Save
                        Promotion</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const promotionFormDefaults = {
            createAction: '{{ route('promotions.store') }}',
            updateBaseUrl: '{{ url('promotions') }}'
        };

        function generatePromotionCode() {
            return 'PROMO-' + Math.random().toString(36).slice(2, 7).toUpperCase();
        }

        function resetPromotionFormState() {
            const form = document.getElementById('promotion-form');

            form.reset();
            form.action = promotionFormDefaults.createAction;
            document.getElementById('promotion-form-method').value = 'POST';
            document.getElementById('promotion-form-modal-title').textContent = 'Create Promotion';
            document.getElementById('promotion-form-submit-button').textContent = 'Save Promotion';
            document.getElementById('promotion-code').value = generatePromotionCode();
            document.getElementById('promotion-rules').value = 'all';
            document.getElementById('promotion-billing-cycle').value = 'monthly';
            document.getElementById('promotion-discount-type').value = 'percentage';
            document.getElementById('promotion-has-specific-product').checked = false;
            updateSpecificLengthVisibility('all');
            syncSpecificProductControls();
            resetIncludedProductsSelection();
            toggleIncludedProductsContainer(false);
            const searchInput = document.getElementById('product-form-included-search');
            if (searchInput) {
                searchInput.value = '';
            }
            filterIncludedProductsOptions();
        }

        function resetIncludedProductsSelection() {
            const includedProducts = document.getElementById('product-form-included-products');
            if (!includedProducts) {
                return;
            }

            Array.from(includedProducts.options).forEach(option => {
                option.selected = false;
            });
        }

        function toggleIncludedProductsContainer(enabled) {
            const container = document.getElementById('product-form-included-products-container');
            const select = document.getElementById('product-form-included-products');
            const rulesValue = document.getElementById('promotion-rules')?.value;
            const canShow = enabled && rulesValue && rulesValue !== 'all';

            if (!container || !select) {
                return;
            }

            if (canShow) {
                container.classList.remove('hidden');
                select.disabled = false;
            } else {
                container.classList.add('hidden');
                select.disabled = true;
                resetIncludedProductsSelection();
            }
        }

        function syncSpecificProductControls() {
            const rulesSelect = document.getElementById('promotion-rules');
            const checkbox = document.getElementById('promotion-has-specific-product');
            const hint = document.getElementById('promotion-specific-product-hint');

            if (!rulesSelect || !checkbox) {
                return;
            }

            const allowSpecificProducts = rulesSelect.value !== 'all';
            checkbox.disabled = !allowSpecificProducts;

            if (!allowSpecificProducts) {
                checkbox.checked = false;
            }

            if (hint) {
                hint.textContent = allowSpecificProducts ?
                    'Enable this option to choose specific products for this promotion.' :
                    'Select a rule other than "All" to enable product selection.';
            }

            toggleIncludedProductsContainer(checkbox.checked);
        }

        function filterIncludedProductsOptions() {
            const searchInput = document.getElementById('product-form-included-search');
            const select = document.getElementById('product-form-included-products');

            if (!searchInput || !select) {
                return;
            }

            const keyword = searchInput.value.trim().toLowerCase();
            Array.from(select.options).forEach(option => {
                const optionText = option.text.toLowerCase();
                option.hidden = keyword !== '' && !optionText.includes(keyword);
            });
        }

        function updateSpecificLengthVisibility(rulesValue) {
            const specificLengthWrapper = document.getElementById('promotion-specific-length-wrapper');
            if (!specificLengthWrapper) {
                return;
            }

            if (rulesValue === 'specific_length_of_term') {
                specificLengthWrapper.classList.remove('hidden');
            } else {
                specificLengthWrapper.classList.add('hidden');
                document.getElementById('promotion-specific-length').value = '';
            }
        }

        function openPromotionViewModal() {
            document.getElementById('promotion-view-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function renderPromotionView(data) {
            var promotion = data.promotion

            const discountType = typeof promotion.discount_type === 'object' ? promotion.discount_type?.value : promotion
                .discount_type;
            const discountValue = promotion.discount_value ? Number(promotion.discount_value) : 0;
            const discountLabel = discountType === 'fixed_amount' || discountType === 'amount' ?
                `IDR ${discountValue.toLocaleString()}` :
                `${discountValue.toLocaleString()}%`;
            const promotionRules = typeof promotion.promotion_rules === 'object' ?
                (promotion.promotion_rules?.value || '-') :
                (promotion.promotion_rules || '-');
            const billingCycle = typeof promotion.billing_cycle === 'object' ?
                (promotion.billing_cycle?.value || '-') :
                (promotion.billing_cycle || '-');

            document.getElementById('promotion-view-code').textContent = promotion.code || '-';
            document.getElementById('promotion-view-name').textContent = promotion.name || '-';
            document.getElementById('promotion-view-type').textContent = promotionRules.replaceAll('_', ' ');
            document.getElementById('promotion-view-billing-cycle').textContent = discountLabel;
            document.getElementById('promotion-view-price-location').textContent = promotion.start_date && promotion
                .end_date ?
                `${promotion.start_date} - ${promotion.end_date}` : '-';
            document.getElementById('promotion-view-price-user').textContent = billingCycle;
            document.getElementById('promotion-view-tax').textContent = promotion.has_specific_product ? 'Yes' : 'No';
            document.getElementById('promotion-view-description').textContent = promotion.description || '-';

            openPromotionViewModal();
        }

        function viewPromotion(promotionId) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('{{ url('promotions') }}/' + promotionId, {
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
                    renderPromotionView(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load promotion data.');
                });
        }

        async function deletePromotion(promotionId, promotionName) {
            const confirmed = window.confirm(
                `Delete promotion "${promotionName}"? This action cannot be undone.`
            );

            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('promotions') }}/${promotionId}`, {
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

                alert(result.message || 'Promotion deleted successfully.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to delete promotion.');
            }
        }

        async function restorePromotion(promotionId, promotionName) {
            const confirmed = window.confirm(`Restore promotion "${promotionName}"?`);
            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('promotions') }}/${promotionId}/restore`, {
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

                alert(result.message || 'Promotion restored successfully.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to restore promotion.');
            }
        }

        async function permanentDeletePromotion(promotionId, promotionName) {
            const confirmed = window.confirm(
                `Permanently delete promotion "${promotionName}"? This cannot be undone and will remove all data.`
            );
            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('promotions') }}/${promotionId}/permanent-delete`, {
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

                alert(result.message || 'Promotion permanently deleted.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to permanently delete promotion.');
            }
        }

        document.getElementById('close-promotion-view-modal').addEventListener('click', function() {
            closeModal('promotion-view-modal');
        });

        document.getElementById('close-promotion-view-modal-cta').addEventListener('click', function() {
            closeModal('promotion-view-modal');
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal('promotion-view-modal');
                closeModal('promotion-form-modal');
            }
        });

        function openPromotionFormModal() {
            resetPromotionFormState();
            document.getElementById('promotion-form-modal').classList.remove('hidden');
        }

        async function editPromotion(promotionId) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`${promotionFormDefaults.updateBaseUrl}/${promotionId}/edit`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw errorData;
                }

                const promotion = await response.json();
                const form = document.getElementById('promotion-form');

                console.log(promotion);

                resetPromotionFormState();
                form.action = `${promotionFormDefaults.updateBaseUrl}/${promotion.id}`;
                document.getElementById('promotion-form-method').value = 'PUT';
                document.getElementById('promotion-form-modal-title').textContent = 'Edit Promotion';
                document.getElementById('promotion-form-submit-button').textContent = 'Update Promotion';

                document.getElementById('promotion-code').value = promotion.code || '';
                document.getElementById('promotion-name').value = promotion.name || '';
                document.getElementById('promotion-description').value = promotion.description || '';
                document.getElementById('promotion-start-date').value = promotion.start_date.split('T')[0];
                document.getElementById('promotion-end-date').value = promotion.end_date.split('T')[0];
                document.getElementById('promotion-rules').value = typeof promotion.promotion_rules === 'object' ?
                    (promotion.promotion_rules?.value || 'all') : (promotion.promotion_rules || 'all');
                document.getElementById('promotion-billing-cycle').value = typeof promotion.billing_cycle === 'object' ?
                    (promotion.billing_cycle?.value || 'monthly') : (promotion.billing_cycle || 'monthly');
                document.getElementById('promotion-specific-length').value = promotion.specific_length_of_term ?? '';
                document.getElementById('promotion-discount-type').value = typeof promotion.discount_type === 'object' ?
                    (promotion.discount_type?.value || 'percentage') : (promotion.discount_type || 'percentage');
                document.getElementById('promotion-discount-value').value = promotion.discount_value ?? '';
                document.getElementById('promotion-has-specific-product').checked = !!promotion.has_specific_product;
                updateSpecificLengthVisibility(document.getElementById('promotion-rules').value);
                syncSpecificProductControls();

                const includedProducts = document.getElementById('product-form-included-products');
                if (includedProducts && Array.isArray(promotion.products)) {
                    const includedIds = promotion.products.map(item => String(item.id));
                    Array.from(includedProducts.options).forEach(option => {
                        option.selected = includedIds.includes(option.value);
                    });
                }
                filterIncludedProductsOptions();

                document.getElementById('promotion-form-modal').classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load promotion data for editing.');
            }
        }

        function closePromotionFormModal() {
            document.getElementById('promotion-form-modal').classList.add('hidden');
        }

        document.getElementById('close-promotion-form-modal').addEventListener('click', function() {
            closePromotionFormModal();
        });

        document.getElementById('close-promotion-form-modal-cta').addEventListener('click', function() {
            closePromotionFormModal();
        });

        document.getElementById('promotion-rules').addEventListener('change', function(event) {
            updateSpecificLengthVisibility(event.target.value);
            syncSpecificProductControls();
        });

        document.getElementById('product-form-included-search').addEventListener('input', function() {
            filterIncludedProductsOptions();
        });
    </script>

@endsection
