@extends('components.layout.main')

@section('title', isset($renewSubscription) && $renewSubscription ? 'Renew Subscription' : 'Create Subscription')

@section('content')
    @php
        $isRenew = isset($renewSubscription) && $renewSubscription;

        $selectedTenantId = old('tenant_id', $isRenew ? $renewSubscription->tenant_id : '');
        $selectedProductIds = collect(
            old('product_ids', $isRenew ? $renewSubscription->products->pluck('id')->all() : []),
        )
            ->map(fn($id) => (int) $id)
            ->all();

        $priceType = old('price_type', $isRenew ? $renewSubscription->price_type : 'per_user');
        $billingCycle = old(
            'billing_cycle',
            $isRenew
                ? (is_object($renewSubscription->billing_cycle)
                    ? $renewSubscription->billing_cycle->value
                    : $renewSubscription->billing_cycle)
                : 'monthly',
        );
        $quantity = old('quantity', $isRenew ? $renewSubscription->quantity : 1);
        $lengthOfTerm = old('length_of_term', $isRenew ? $renewSubscription->length_of_term : 1);

        $startDateDefault =
            $isRenew && $renewSubscription->end_date
                ? $renewSubscription->end_date->copy()->addDay()->format('Y-m-d')
                : now()->format('Y-m-d');

        $startDate = old('start_date', $startDateDefault);

        $referenceCode = old('reference_code', $isRenew ? optional($renewSubscription->promotion)->code : '');

        $formAction = $isRenew ? route('subscriptions.renew', $renewSubscription->id) : route('subscriptions.store');
    @endphp

    <div class="space-y-6">

        <!-- Free Trial Header -->
        <div class="bg-gradient-to-r from-[#034c8f] to-[#00a8e3] rounded-lg shadow-lg p-6 text-white">
            <h1 class="text-3xl font-bold mb-2">{{ 'Hi, ' . Auth::user()->name }}</h1>
            <p class="text-white-600">{{ $isRenew ? 'Renew Subscription' : 'Create Subscription' }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="rounded-lg bg-blue-50 border border-blue-100 p-4 text-blue-900 text-sm">
                Fill the subscription information below. Price, tax, discount, and total are calculated automatically.
            </div>

            <h2 class="text-xl font-semibold text-gray-900 mb-6">Subscription Form</h2>

            <form method="POST" action="{{ $formAction }}" id="subscription-form" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-2 gap-2">
                            <label for="subscription-tenant-id" class="block text-sm font-medium text-gray-700">Tenant &
                                Domain</label>
                            @if (!$isEmployee)
                                <button type="button" id="subscription-open-create-tenant"
                                    class="inline-flex items-center px-3 py-1.5 text-xs rounded-lg bg-blue-100 text-blue-700 font-semibold hover:bg-blue-200 transition">
                                    + Create New Tenant
                                </button>
                            @endif
                        </div>
                        <select id="subscription-tenant-id" name="tenant_id" required
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('tenant_id') border-red-500 @else border-gray-300 @enderror">
                            <option value="">Select tenant</option>
                            @foreach ($availableTenants ?? collect() as $tenantOption)
                                <option value="{{ $tenantOption->id }}" @selected((string) $selectedTenantId === (string) $tenantOption->id)>
                                    {{ $tenantOption->name }} ({{ $tenantOption->domain ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('tenant_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subscription-products"
                            class="block text-sm font-medium text-gray-700 mb-2">Products</label>
                        <input id="subscription-products-search" type="text" placeholder="Search products..."
                            class="w-full mb-2 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition">
                        <select id="subscription-products" name="product_ids[]" multiple required
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition h-36 @error('product_ids') border-red-500 @else border-gray-300 @enderror">
                            @foreach ($availableProducts ?? collect() as $productOption)
                                <option value="{{ $productOption->id }}" @selected(in_array((int) $productOption->id, $selectedProductIds, true))>
                                    {{ $productOption->name }} - Rp
                                    {{ number_format($productOption->price_per_user, 0, ',', '.') }} per user /
                                    {{ number_format($productOption->price_per_location, 0, ',', '.') }} per location
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Cmd/Ctrl to select multiple products.</p>
                        @error('product_ids')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subscription-price-type" class="block text-sm font-medium text-gray-700 mb-2">Price
                            Type</label>
                        <select id="subscription-price-type" name="price_type"
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('price_type') border-red-500 @else border-gray-300 @enderror">
                            <option value="per_user" @selected($priceType === 'per_user')>Per User</option>
                            <option value="per_location" @selected($priceType === 'per_location')>Per Location</option>
                        </select>
                        @error('price_type')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subscription-billing-cycle" class="block text-sm font-medium text-gray-700 mb-2">Billing
                            Cycle</label>
                        <select id="subscription-billing-cycle" name="billing_cycle" required
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('billing_cycle') border-red-500 @else border-gray-300 @enderror">
                            <option value="monthly" @selected($billingCycle === 'monthly')>Monthly</option>
                            <option value="yearly" @selected($billingCycle === 'yearly')>Yearly</option>
                        </select>
                        @error('billing_cycle')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subscription-length-of-term" class="block text-sm font-medium text-gray-700 mb-2">Length
                            of
                            Term</label>
                        <input id="subscription-length-of-term" name="length_of_term" type="number" min="1"
                            step="1" required value="{{ $lengthOfTerm }}"
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('length_of_term') border-red-500 @else border-gray-300 @enderror">
                        @error('length_of_term')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subscription-quantity"
                            class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input id="subscription-quantity" name="quantity" type="number" min="1" step="1"
                            required value="{{ $quantity }}"
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('quantity') border-red-500 @else border-gray-300 @enderror">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subscription-start-date" class="block text-sm font-medium text-gray-700 mb-2">Start
                            Date</label>
                        <input id="subscription-start-date" name="start_date" type="date" required
                            value="{{ $startDate }}"
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('start_date') border-red-500 @else border-gray-300 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subscription-end-date" class="block text-sm font-medium text-gray-700 mb-2">End Date
                            (Auto)</label>
                        <input id="subscription-end-date" type="date" readonly
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                    </div>

                    <div class="md:col-span-2">
                        <label for="subscription-reference-code"
                            class="block text-sm font-medium text-gray-700 mb-2">Reference
                            Code</label>
                        <div class="flex gap-2">
                            <input id="subscription-reference-code" name="reference_code" type="text"
                                value="{{ $referenceCode }}" placeholder="Enter promotion/reference code"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('reference_code') border-red-500 @else border-gray-300 @enderror">
                            <button type="button" id="subscription-check-reference"
                                class="px-4 py-2.5 rounded-lg bg-blue-100 text-slate-700 text-sm font-medium hover:bg-blue-200">
                                Check
                            </button>
                        </div>
                        <p id="subscription-reference-status" class="mt-1 text-xs text-gray-500">No code applied.</p>
                        @error('reference_code')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subscription-customer-phone"
                            class="block text-sm font-medium text-gray-700 mb-2">Customer Phone</label>
                        <input id="subscription-customer-phone" name="customer_phone" type="text" required
                            value="{{ $customerPhone }}"
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('customer_phone') border-red-500 @else border-gray-300 @enderror">
                        @error('customer_phone')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 font-medium text-slate-700 bg-slate-50">Base Price</td>
                                <td id="subscription-calc-base" class="px-4 py-2 text-right text-slate-800">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium text-slate-700 bg-slate-50">Tax</td>
                                <td id="subscription-calc-tax" class="px-4 py-2 text-right text-slate-800">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium text-slate-700 bg-slate-50">Discount</td>
                                <td id="subscription-calc-discount" class="px-4 py-2 text-right text-slate-800">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold text-slate-900 bg-slate-100">Total</td>
                                <td id="subscription-calc-total"
                                    class="px-4 py-2 text-right font-semibold text-slate-900">Rp 0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex gap-4 pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2.5 text-white font-semibold rounded-lg transition duration-200 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="background-color: #034c8f;">
                        {{ $isRenew ? 'Renew Subscription' : 'Create Subscription' }}
                    </button>
                    <a href="{{ route('subscriptions.index') }}"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Create Tenant Modal (From Subscription Form) -->
        @if (!$isEmployee)
            <div id="subscription-create-tenant-modal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
                <div
                    class="bg-white rounded-xl w-full max-w-xl sm:max-w-2xl shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
                    <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-bold">Create New Tenant</h3>
                        <button type="button" id="subscription-close-create-tenant-modal"
                            class="text-white hover:text-slate-200">✕</button>
                    </div>

                    <form id="subscription-create-tenant-form" class="p-6 space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="subscription-tenant-code"
                                    class="block text-sm font-medium text-gray-700 mb-2">Tenant Code</label>
                                <input type="text" id="subscription-tenant-code" name="code"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition"
                                    placeholder="Auto generated" readonly>
                                <p id="subscription-tenant-code-error" class="mt-1 text-sm text-red-600 hidden"></p>
                            </div>
                            <div>
                                <label for="subscription-tenant-name"
                                    class="block text-sm font-medium text-gray-700 mb-2">Tenant Name</label>
                                <input type="text" id="subscription-tenant-name" name="name"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition"
                                    placeholder="My School / My Business" required>
                                <p id="subscription-tenant-name-error" class="mt-1 text-sm text-red-600 hidden"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="subscription-tenant-domain"
                                    class="block text-sm font-medium text-gray-700 mb-2">Tenant Domain</label>
                                <div
                                    class="flex items-center rounded-lg border border-gray-300 focus-within:ring-2 focus-within:ring-[#034c8f] focus-within:border-transparent transition">
                                    <input type="text" id="subscription-tenant-domain" name="domain"
                                        class="w-full border-0 rounded-l-lg px-4 py-2.5 focus:ring-0"
                                        placeholder="your-domain" autocomplete="off" required>
                                    <span
                                        class="px-3 text-sm text-gray-500 border-l border-gray-200 py-2.5">.rakomsis.com</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Preview: <span
                                        id="subscription-tenant-domain-preview">your-domain.rakomsis.com</span></p>
                                <p id="subscription-tenant-domain-error" class="mt-1 text-sm text-red-600 hidden"></p>
                            </div>
                            <div>
                                <label for="subscription-tenant-business-type"
                                    class="block text-sm font-medium text-gray-700 mb-2">Business Type</label>
                                <input type="text" id="subscription-tenant-business-type" name="business_type"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition"
                                    placeholder="School, Retail, Services, etc.">
                                <p id="subscription-tenant-business-type-error" class="mt-1 text-sm text-red-600 hidden">
                                </p>
                            </div>
                        </div>

                        <div>
                            <label for="subscription-tenant-address"
                                class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" id="subscription-tenant-address" name="address"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition"
                                placeholder="Tenant address">
                            <p id="subscription-tenant-address-error" class="mt-1 text-sm text-red-600 hidden"></p>
                        </div>
                    </form>

                    <div class="px-6 py-4 border-t border-gray-200 flex gap-2 justify-end">
                        <button type="button" id="subscription-cancel-create-tenant"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="button" id="subscription-submit-create-tenant"
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                            Create Tenant
                        </button>
                    </div>
                </div>
            </div>
        @endif
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

        function formatCurrency(amountValue) {
            const parsed = Number(amountValue);
            if (Number.isNaN(parsed)) {
                return 'Rp 0';
            }

            return `Rp ${parsed.toLocaleString('id-ID')}`;
        }

        function getSelectedProductIds() {
            const select = document.getElementById('subscription-products');
            return Array.from(select.selectedOptions).map(option => Number(option.value)).filter(Boolean);
        }

        function filterProductOptions() {
            const searchInput = document.getElementById('subscription-products-search');
            const select = document.getElementById('subscription-products');
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
            const statusNode = document.getElementById('subscription-reference-status');
            const code = document.getElementById('subscription-reference-code').value;
            const promotion = findPromotionByCode(code);

            if (!promotion) {
                statusNode.textContent = code ? 'Reference code not found or inactive.' : 'No code applied.';
                statusNode.className = code ? 'mt-1 text-xs text-red-600' : 'mt-1 text-xs text-gray-500';
                return null;
            }

            const billingCycle = document.getElementById('subscription-billing-cycle').value;
            const lengthOfTerm = Number(document.getElementById('subscription-length-of-term').value || 1);
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

        function calculateEndDate() {
            const startDate = document.getElementById('subscription-start-date').value;
            const lengthOfTerm = Number(document.getElementById('subscription-length-of-term').value || 1);
            const billingCycle = document.getElementById('subscription-billing-cycle').value;
            const endDateInput = document.getElementById('subscription-end-date');

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

        function recalculateSummary() {
            const productIds = getSelectedProductIds();
            const priceType = document.getElementById('subscription-price-type').value;
            const quantity = Number(document.getElementById('subscription-quantity').value || 1);
            const lengthOfTerm = Number(document.getElementById('subscription-length-of-term').value || 1);

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

            document.getElementById('subscription-calc-base').textContent = formatCurrency(basePrice);
            document.getElementById('subscription-calc-tax').textContent = formatCurrency(tax);
            document.getElementById('subscription-calc-discount').textContent = formatCurrency(discount);
            document.getElementById('subscription-calc-total').textContent = formatCurrency(total);
        }

        document.getElementById('subscription-start-date').addEventListener('change', function() {
            calculateEndDate();
            recalculateSummary();
        });
        document.getElementById('subscription-length-of-term').addEventListener('input', function() {
            calculateEndDate();
            recalculateSummary();
        });
        document.getElementById('subscription-billing-cycle').addEventListener('change', function() {
            calculateEndDate();
            recalculateSummary();
        });
        document.getElementById('subscription-quantity').addEventListener('input', recalculateSummary);
        document.getElementById('subscription-price-type').addEventListener('change', recalculateSummary);
        document.getElementById('subscription-products').addEventListener('change', recalculateSummary);
        document.getElementById('subscription-products-search').addEventListener('input', filterProductOptions);
        document.getElementById('subscription-reference-code').addEventListener('input', recalculateSummary);
        document.getElementById('subscription-check-reference').addEventListener('click', recalculateSummary);

        @if (!$isEmployee)
            function generateSubscriptionTenantCode() {
                const randomPart = Math.random().toString(36).slice(2, 8).toUpperCase();
                return `TEN-${randomPart.padEnd(6, '0').slice(0, 6)}`;
            }

            function clearSubscriptionTenantCreateErrors() {
                const fieldKeys = ['code', 'name', 'domain', 'address', 'business-type'];

                fieldKeys.forEach(key => {
                    const input = document.getElementById(`subscription-tenant-${key}`);
                    const error = document.getElementById(`subscription-tenant-${key}-error`);

                    if (input) {
                        input.classList.remove('border-red-400');
                    }

                    if (error) {
                        error.textContent = '';
                        error.classList.add('hidden');
                    }
                });
            }

            function setSubscriptionTenantCreateFieldError(field, message) {
                const fieldMap = {
                    code: 'code',
                    name: 'name',
                    domain: 'domain',
                    address: 'address',
                    business_type: 'business-type',
                };

                const key = fieldMap[field] || field;
                const input = document.getElementById(`subscription-tenant-${key}`);
                const error = document.getElementById(`subscription-tenant-${key}-error`);

                if (input) {
                    input.classList.add('border-red-400');
                }

                if (error) {
                    error.textContent = message;
                    error.classList.remove('hidden');
                }
            }

            function updateSubscriptionTenantDomainPreview() {
                const domainInput = document.getElementById('subscription-tenant-domain');
                const preview = document.getElementById('subscription-tenant-domain-preview');

                if (!domainInput || !preview) {
                    return;
                }

                const cleaned = String(domainInput.value || '')
                    .toLowerCase()
                    .replace(/[^a-z0-9-]/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');

                if (domainInput.value !== cleaned) {
                    domainInput.value = cleaned;
                }

                preview.textContent = cleaned ? `${cleaned}.rakomsis.com` : 'your-domain.rakomsis.com';
            }

            function openSubscriptionTenantCreateModal() {
                document.getElementById('subscription-create-tenant-form').reset();
                clearSubscriptionTenantCreateErrors();
                const codeInput = document.getElementById('subscription-tenant-code');
                if (codeInput) {
                    codeInput.value = generateSubscriptionTenantCode();
                }
                updateSubscriptionTenantDomainPreview();
                document.getElementById('subscription-create-tenant-modal').classList.remove('hidden');
            }

            function closeSubscriptionTenantCreateModal() {
                document.getElementById('subscription-create-tenant-modal').classList.add('hidden');
            }

            function upsertSubscriptionTenantOption(tenant) {
                const tenantSelect = document.getElementById('subscription-tenant-id');
                const value = String(tenant.id);
                const label = `${tenant.name} (${tenant.domain || '-'})`;

                const existing = Array.from(tenantSelect.options).find(option => option.value === value);

                if (existing) {
                    existing.textContent = label;
                } else {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = label;
                    tenantSelect.appendChild(option);
                }

                tenantSelect.value = value;
            }

            async function submitSubscriptionTenantCreateForm() {
                const form = document.getElementById('subscription-create-tenant-form');
                const formData = new FormData(form);
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                clearSubscriptionTenantCreateErrors();

                try {
                    const response = await fetch('{{ route('tenants.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token || '',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && result.errors) {
                            Object.entries(result.errors).forEach(([field, messages]) => {
                                const firstMessage = Array.isArray(messages) ? messages[0] : messages;
                                setSubscriptionTenantCreateFieldError(field, firstMessage);
                            });
                        }

                        throw result;
                    }

                    if (result.tenant) {
                        upsertSubscriptionTenantOption(result.tenant);
                    }

                    closeSubscriptionTenantCreateModal();
                } catch (error) {
                    console.error('Error:', error);
                    if (!error?.errors) {
                        alert(error.message || 'Failed to create tenant.');
                    }
                }
            }

            document.getElementById('subscription-open-create-tenant')?.addEventListener('click',
                openSubscriptionTenantCreateModal);
            document.getElementById('subscription-close-create-tenant-modal')?.addEventListener('click',
                closeSubscriptionTenantCreateModal);
            document.getElementById('subscription-cancel-create-tenant')?.addEventListener('click',
                closeSubscriptionTenantCreateModal);
            document.getElementById('subscription-submit-create-tenant')?.addEventListener('click',
                submitSubscriptionTenantCreateForm);
            document.getElementById('subscription-tenant-domain')?.addEventListener('input',
                updateSubscriptionTenantDomainPreview);
        @endif

        calculateEndDate();
        recalculateSummary();
    </script>
@endsection
