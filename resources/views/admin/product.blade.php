@extends('components.layout.main')

@section('title', 'Product Management')

@section('content')
    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('products.index') }}" class="flex gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Products</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Search by name or code..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="w-48">
                    <label for="product_type" class="block text-sm font-medium text-gray-700 mb-1">Product Type</label>
                    <select name="product_type" id="product_type"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="single" {{ request('product_type') === 'single' ? 'selected' : '' }}>Single Product
                        </option>
                        <option value="bundle" {{ request('product_type') === 'bundle' ? 'selected' : '' }}>Product Bundle
                        </option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filter
                    </button>
                    @if (request('search') || request('product_type'))
                        <a href="{{ route('products.index') }}"
                            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Products ({{ $products->total() }})</h3>
                <button type="button" onclick="openProductFormModal()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                        </path>
                    </svg>
                    Add New Product
                </button>
            </div>

            @if ($products->count() > 0)
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
                                    Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Billing Cycle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tax</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button type="button" onclick="viewProduct({{ $product->id }})"
                                                class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                                View
                                            </button>
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $product->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $priceText = '';
                                            if ($product->price_per_location > 0) {
                                                $priceText .=
                                                    'IDR' .
                                                    number_format($product->price_per_location) .
                                                    ' per location';
                                            }
                                            if ($product->price_per_user > 0) {
                                                if ($priceText) {
                                                    $priceText .= ' OR ';
                                                }
                                                $priceText .=
                                                    'IDR' . number_format($product->price_per_user) . ' per user';
                                            }
                                        @endphp
                                        {{ $priceText ?: 'Free' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->billing_cycle->description() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->product_type->description() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->tax_percentage ? $product->tax_percentage . '%' : 'No tax' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $product->created_at->format('M d, Y H:i') }}
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
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of
                            {{ $products->total() }} results
                        </div>
                        <div class="flex-1 flex justify-end">
                            {{ $products->appends(request()->query())->links('vendor.pagination.tailwind') }}
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if (request('search'))
                            No products match your search criteria.
                        @else
                            Get started by creating your first product.
                        @endif
                    </p>
                    @if (request('search'))
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Clear Search
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Product Modal -->
    {{-- <div x-show="showProductModal" x-clock @keydown.escape.window="closeProductModal()" @click.self="closeProductModal()"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-medium text-gray-900" x-text="modalTitle">Add New Product</h3>
                <button type="button" @click="closeProductModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitProductForm()" class="p-4">
                @csrf
                <input type="hidden" name="_method" x-bind:value="formMethod">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Product Code</label>
                        <input type="text" x-model="form.code" name="code" id="code" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                        <input type="text" x-model="form.name" name="name" id="name" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea x-model="form.description" name="description" id="description" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div>
                        <label for="price_per_location" class="block text-sm font-medium text-gray-700">Price per Location
                            ($)</label>
                        <input type="number" x-model="form.price_per_location" name="price_per_location"
                            id="price_per_location" step="0.01" min="0"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="price_per_user" class="block text-sm font-medium text-gray-700">Price per User
                            (IDR)</label>
                        <input type="number" x-model="form.price_per_user" name="price_per_user" id="price_per_user"
                            step="0.01" min="0"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="tax_percentage" class="block text-sm font-medium text-gray-700">Tax Percentage
                            (IDR)</label>
                        <input type="number" x-model="form.tax_percentage" name="tax_percentage" id="tax_percentage"
                            step="0.01" min="0" max="100" value="11"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="billing_cycle" class="block text-sm font-medium text-gray-700">Billing Cycle</label>
                        <select x-model="form.billing_cycle" name="billing_cycle" id="billing_cycle" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>

                    <div>
                        <label for="product_type" class="block text-sm font-medium text-gray-700">Product Type</label>
                        <select x-model="form.product_type" name="product_type" id="product_type" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="single">Single Product</option>
                            <option value="bundle">Product Bundle</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                    <button type="button" @click="closeProductModal()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" :disabled="loading"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="loading ? 'Saving...' : (form.id ? 'Update Product' : 'Create Product')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div> --}}

    <!-- View Product Modal -->
    <div id="product-view-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
        <div
            class="bg-white rounded-xl w-full max-w-xl sm:max-w-2xl lg:max-w-3xl shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                <h3 id="product-view-modal-title" class="text-base sm:text-lg font-bold">Product Details</h3>
                <button id="close-product-view-modal" class="text-white hover:text-slate-200">✕</button>
            </div>

            <div class="flex border-b border-blue-100 bg-blue-50">
                <button id="product-view-tab-details" onclick="setProductViewTab('details')"
                    class="w-1/2 px-4 py-3 text-sm sm:text-base font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none">
                    Details
                </button>
                <button id="product-view-tab-included" onclick="setProductViewTab('included')"
                    class="w-1/2 px-4 py-3 text-sm sm:text-base font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none hidden"
                    disabled>
                    Included Products
                </button>
            </div>

            <div id="product-view-content-details" class="p-6">
                <dl class="grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-2 text-sm text-gray-700">
                    <div>
                        <dt class="font-semibold">Code</dt>
                        <dd id="product-view-code">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Name</dt>
                        <dd id="product-view-name">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Type</dt>
                        <dd id="product-view-type">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Billing Cycle</dt>
                        <dd id="product-view-billing-cycle">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Price per Location</dt>
                        <dd id="product-view-price-location">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Price per User</dt>
                        <dd id="product-view-price-user">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Tax</dt>
                        <dd id="product-view-tax">-</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-semibold">Description</dt>
                        <dd id="product-view-description">-</dd>
                    </div>
                </dl>
            </div>

            <div id="product-view-content-included" class="hidden p-6">
                <p id="product-view-included-empty" class="text-sm text-gray-500">No included products are available.</p>
                <div class="mt-4 overflow-x-auto hidden" id="product-view-included-table-wrapper">
                    <table class="min-w-full text-sm text-left text-blue-900 border border-blue-200">
                        <thead class="bg-blue-50 text-blue-900 border-b border-blue-300">
                            <tr>
                                <th class="px-3 py-2 border-r border-blue-200">Code</th>
                                <th class="px-3 py-2 border-r border-blue-200">Name</th>
                                <th class="px-3 py-2">Type</th>
                            </tr>
                        </thead>
                        <tbody id="product-view-included-list"></tbody>
                    </table>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 text-right">
                <button id="close-product-view-modal-cta" onclick="closeModal('product-view-modal')"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Close</button>
            </div>
        </div>
    </div>

    <!-- Product Form Modal -->
    <div id="product-form-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
        <div
            class="bg-white rounded-xl w-full max-w-md sm:max-w-xl lg:max-w-2xl shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-bold">Create Product</h3>
                <button id="close-product-form-modal" class="text-white hover:text-slate-200">✕</button>
            </div>

            <form id="product-form" class="p-6 space-y-4" method="POST" action="{{ route('products.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Code</label>
                        <input type="text" name="code"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Name</label>
                        <input type="text" name="name"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-blue-900">Description</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2"></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Price per Location</label>
                        <input type="number" step="0.01" min="0" name="price_per_location"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Price per User</label>
                        <input type="number" step="0.01" min="0" name="price_per_user"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Tax Percentage</label>
                        <input type="number" step="0.01" min="0" max="100" name="tax_percentage"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Billing Cycle</label>
                        <select name="billing_cycle"
                            class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Product Type</label>
                        <select name="product_type" class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                            <option value="single">Single Product</option>
                            <option value="bundle">Product Bundle</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-blue-900">Tax Status</label>
                        <select name="tax_status" class="mt-1 block w-full border border-blue-200 rounded-md px-3 py-2">
                            <option value="1">Taxable</option>
                            <option value="0">Non-taxable</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-blue-100">
                    <button type="button" id="close-product-form-modal-cta"
                        class="px-4 py-2 rounded-md border border-blue-300 text-blue-700 hover:bg-blue-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Save
                        Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setProductViewTab(tab) {
            const detailsTab = document.getElementById('product-view-tab-details');
            const includedTab = document.getElementById('product-view-tab-included');
            const detailsContent = document.getElementById('product-view-content-details');
            const includedContent = document.getElementById('product-view-content-included');

            if (tab === 'included') {
                detailsTab.classList.remove('bg-white', 'text-blue-700');
                detailsTab.classList.add('bg-gray-100', 'text-gray-700');
                includedTab.classList.add('bg-white', 'text-blue-700');
                includedTab.classList.remove('bg-gray-100', 'text-gray-700');
                detailsContent.classList.add('hidden');
                includedContent.classList.remove('hidden');
            } else {
                detailsTab.classList.add('bg-white', 'text-blue-700');
                detailsTab.classList.remove('bg-gray-100', 'text-gray-700');
                includedTab.classList.remove('bg-white', 'text-blue-700');
                includedTab.classList.add('bg-gray-100', 'text-gray-700');
                detailsContent.classList.remove('hidden');
                includedContent.classList.add('hidden');
            }
        }

        function openProductViewModal() {
            document.getElementById('product-view-modal').classList.remove('hidden');
            setProductViewTab('details');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function renderProductView(data) {
            var product = data.product

            document.getElementById('product-view-code').textContent = product.code || '-';
            document.getElementById('product-view-name').textContent = product.name || '-';
            document.getElementById('product-view-type').textContent = (product.product_type && product.product_type
                .description) ? product.product_type.description : (product.product_type || '-');
            document.getElementById('product-view-billing-cycle').textContent = (product.billing_cycle && product
                .billing_cycle.description) ? product.billing_cycle.description : (product.billing_cycle || '-');
            document.getElementById('product-view-price-location').textContent = product.price_per_location ? 'IDR ' +
                Number(
                    product.price_per_location).toLocaleString() : 'Free';
            document.getElementById('product-view-price-user').textContent = product.price_per_user ? 'IDR ' + Number(
                product
                .price_per_user).toLocaleString() : 'Free';
            document.getElementById('product-view-tax').textContent = product.tax_percentage ? Number(
                    product.tax_percentage) +
                '%' : 'No tax';
            document.getElementById('product-view-description').textContent = product.description || '-';

            const includedButton = document.getElementById('product-view-tab-included');
            const tableWrapper = document.getElementById('product-view-included-table-wrapper');
            const includedEmpty = document.getElementById('product-view-included-empty');
            const includedList = document.getElementById('product-view-included-list');

            if (Array.isArray(product.included_products) && product.included_products.length > 0) {
                includedButton.classList.remove('hidden');
                includedButton.disabled = false;
                includedEmpty.classList.add('hidden');
                tableWrapper.classList.remove('hidden');

                includedList.innerHTML = product.included_products.map(item => {
                    const code = item.code || '-';
                    const name = item.name || '-';
                    const type = (item.product_type && item.product_type.description) ? item.product_type
                        .description : (item.product_type || '-');

                    return `<tr class="border-t border-gray-100"><td class="px-3 py-2">${code}</td><td class="px-3 py-2">${name}</td><td class="px-3 py-2">${type}</td></tr>`;
                }).join('');
            } else {
                includedButton.classList.add('hidden');
                includedButton.disabled = true;
                includedEmpty.classList.remove('hidden');
                tableWrapper.classList.add('hidden');
            }

            openProductViewModal();
        }

        function viewProduct(productId) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('{{ url('products') }}/' + productId, {
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
                    renderProductView(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load product data.');
                });
        }

        document.getElementById('close-product-view-modal').addEventListener('click', function() {
            closeModal('product-view-modal');
        });

        document.getElementById('close-product-view-modal-cta').addEventListener('click', function() {
            closeModal('product-view-modal');
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal('product-view-modal');
                closeModal('product-form-modal');
            }
        });

        function openProductFormModal() {
            document.getElementById('product-form-modal').classList.remove('hidden');
        }

        function closeProductFormModal() {
            document.getElementById('product-form-modal').classList.add('hidden');
        }

        document.getElementById('close-product-form-modal').addEventListener('click', function() {
            closeProductFormModal();
        });

        document.getElementById('close-product-form-modal-cta').addEventListener('click', function() {
            closeProductFormModal();
        });
    </script>

@endsection
