@extends('components.layout.main')

@section('title', 'Tenant Management')

@section('content')
    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('tenants.index') }}" class="flex gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Tenants</label>
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
                        <a href="{{ route('tenants.index') }}"
                            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tenants Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $showDeleted ? 'Deleted Tenants' : 'Tenants' }} ({{ $tenants->total() }})
                </h3>
                @if (!Auth::user()->isAdmin() && !$showDeleted)
                    <button type="button" onclick="openNewTenantModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                            </path>
                        </svg>
                        New Tenant
                    </button>
                @endif
            </div>

            @if ($tenants->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Has Active Subscription</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customers</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Domain</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($tenants as $tenant)
                                <tr class="{{ $tenant->trashed() ? 'bg-red-50 opacity-75' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if ($tenant->trashed())
                                                <button type="button"
                                                    onclick="restoreTenant({{ $tenant->id }}, @js($tenant->name))"
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
                                                    onclick="permanentDeleteTenant({{ $tenant->id }}, @js($tenant->name))"
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
                                                <button type="button"
                                                    onclick="inviteOtherCustomer({{ $tenant->id }}, @js($tenant->name))"
                                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                    Invite Other Customer
                                                </button>
                                                <button type="button" onclick="viewTenant({{ $tenant->id }})"
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
                                                <button type="button"
                                                    onclick="deleteTenant({{ $tenant->id }}, @js($tenant->name))"
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
                                        {{ $tenant->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @foreach ($tenant->subscriptions as $subscription)
                                            @if ($subscription->subscription_status === 'active')
                                                <a href="{{ route('subscription') }}"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 mr-1 mb-1">
                                                    {{ $subscription->code }} -
                                                    {{ $subscription->product()->pluck('name')->join(', ') }}
                                                </a>
                                                @break
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="max-w-xs truncate {{ $tenant->trashed() ? 'line-through text-gray-400' : '' }}"
                                            title="{{ $tenant->name }}">
                                            {{ $tenant->name }}
                                        </div>
                                        @if ($tenant->trashed())
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 mt-1">
                                                Deleted {{ $tenant->deleted_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @foreach ($tenant->customers as $customer)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 mr-1 mb-1">
                                                {{ $customer->user->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $tenant->domain }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $tenant->address }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $tenant->created_at->format('M d, Y H:i') }}
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
                            Showing {{ $tenants->firstItem() }} to {{ $tenants->lastItem() }} of
                            {{ $tenants->total() }} results
                        </div>
                        <div class="flex-1 flex justify-end">
                            {{ $tenants->appends(request()->query())->links('vendor.pagination.tailwind') }}
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tenants found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if (request('search'))
                            No tenants match your search criteria.
                        @else
                            Get started by creating your first tenant.
                        @endif
                    </p>
                    @if (request('search'))
                        <div class="mt-6">
                            <a href="{{ route('tenants.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Clear Search
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Invite Customer Modal -->
    <div id="tenant-invite-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
        <div
            class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-bold">Invite Other Customer</h3>
                <button onclick="closeModal('tenant-invite-modal')" class="text-white hover:text-slate-200">✕</button>
            </div>

            <form id="tenant-invite-form" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="tenant-invite-tenant-id" name="tenant_id">

                <div>
                    <label for="tenant-invite-tenant-name"
                        class="block text-sm font-medium text-gray-700 mb-1">Tenant</label>
                    <input type="text" id="tenant-invite-tenant-name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700" readonly>
                </div>

                <div>
                    <label for="tenant-invite-email" class="block text-sm font-medium text-gray-700 mb-1">Customer
                        Email</label>
                    <input type="email" id="tenant-invite-email" name="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter registered customer email" required>
                </div>

                <div>
                    <label for="tenant-invite-role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="tenant-invite-role" name="role"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
            </form>

            <div class="px-6 py-4 border-t border-gray-200 flex gap-2 justify-end">
                <button onclick="closeModal('tenant-invite-modal')"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="submitTenantInviteForm()"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                    Send Invite
                </button>
            </div>
        </div>
    </div>

    <!-- Create Tenant Modal -->
    <div id="tenant-create-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
        <div
            class="bg-white rounded-xl w-full max-w-xl sm:max-w-2xl shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-bold">Create New Tenant</h3>
                <button onclick="closeModal('tenant-create-modal')" class="text-white hover:text-slate-200">✕</button>
            </div>

            <form id="tenant-create-form" class="p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tenant-code" class="block text-sm font-medium text-gray-700 mb-2">Tenant Code</label>
                        <input type="text" id="tenant-code" name="code"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition"
                            placeholder="Auto generated" readonly>
                        <p id="tenant-code-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>

                    <div>
                        <label for="tenant-name" class="block text-sm font-medium text-gray-700 mb-2">Tenant Name</label>
                        <input type="text" id="tenant-name" name="name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition"
                            placeholder="My School / My Business" required>
                        <p id="tenant-name-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tenant-domain" class="block text-sm font-medium text-gray-700 mb-2">Tenant
                            Domain</label>
                        <div
                            class="flex items-center rounded-lg border border-gray-300 focus-within:ring-2 focus-within:ring-[#034c8f] focus-within:border-transparent transition">
                            <input type="text" id="tenant-domain" name="domain"
                                class="w-full border-0 rounded-l-lg px-4 py-2.5 focus:ring-0" placeholder="your-domain"
                                autocomplete="off" required>
                            <span class="px-3 text-sm text-gray-500 border-l border-gray-200 py-2.5">.rakomsis.com</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Preview: <span
                                id="tenant-domain-preview">your-domain.rakomsis.com</span></p>
                        <p id="tenant-domain-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>

                    <div>
                        <label for="tenant-business-type" class="block text-sm font-medium text-gray-700 mb-2">Business
                            Type</label>
                        <input type="text" id="tenant-business-type" name="business_type"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition"
                            placeholder="School, Retail, Services, etc.">
                        <p id="tenant-business-type-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>
                </div>

                <div>
                    <label for="tenant-address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" id="tenant-address" name="address"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition"
                        placeholder="Tenant address">
                    <p id="tenant-address-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
            </form>

            <div class="px-6 py-4 border-t border-gray-200 flex gap-2 justify-end">
                <button onclick="closeModal('tenant-create-modal')"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="submitCreateTenantForm()"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                    Create Tenant
                </button>
            </div>
        </div>
    </div>

    <!-- View Tenant Modal -->
    <div id="tenant-view-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
        <div
            class="bg-white rounded-xl w-full max-w-xl sm:max-w-2xl lg:max-w-3xl shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                <h3 id="tenant-view-modal-title" class="text-base sm:text-lg font-bold">Tenant Details</h3>
                <button id="close-tenant-view-modal" class="text-white hover:text-slate-200">✕</button>
            </div>

            <div class="flex border-b border-blue-100 bg-blue-50">
                <button id="tenant-view-details-tab" onclick="setTenantViewTab('details')"
                    class="w-1/2 px-4 py-3 text-sm sm:text-base font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none">
                    Details
                </button>
                <button id="tenant-view-customer-related-tab" onclick="setTenantViewTab('included')"
                    class="w-1/2 px-4 py-3 text-sm sm:text-base font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none hidden"
                    disabled>
                    Related Tenants
                </button>
            </div>

            <div id="tenant-view-content-details" class="p-6">
                <dl class="grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-2 text-sm text-gray-700">
                    <div>
                        <dt class="font-semibold">Code</dt>
                        <dd id="tenant-view-code">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Name</dt>
                        <dd id="tenant-view-name">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Domain</dt>
                        <dd id="tenant-view-domain">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Address</dt>
                        <dd id="tenant-view-address">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Business Type</dt>
                        <dd id="tenant-view-business-type">-</dd>
                    </div>
                </dl>
            </div>

            <div id="tenant-view-content-customer-related" class="hidden p-6">
                <p id="tenant-view-customer-related-empty" class="text-sm text-gray-500">No related tenants are available.
                </p>
                <div class="mt-4 overflow-x-auto hidden" id="tenant-view-customer-related-table-wrapper">
                    <table class="min-w-full text-sm text-left text-blue-900 border border-blue-200">
                        <thead class="bg-blue-50 text-blue-900 border-b border-blue-300">
                            <tr>
                                <th class="px-3 py-2 border-r border-blue-200">Code</th>
                                <th class="px-3 py-2 border-r border-blue-200">Domain</th>
                                <th class="px-3 py-2 border-r border-blue-200">Name</th>
                                <th class="px-3 py-2 border-r border-blue-200">Address</th>
                                <th class="px-3 py-2">Type</th>
                            </tr>
                        </thead>
                        <tbody id="tenant-view-customer-related-list"></tbody>
                    </table>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 text-right">
                <button id="close-tenant-view-modal-cta" onclick="closeModal('tenant-view-modal')"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Close</button>
            </div>
        </div>
    </div>

    <script>
        function generateTenantCode() {
            const randomPart = Math.random().toString(36).slice(2, 8).toUpperCase();
            return `TEN-${randomPart.padEnd(6, '0').slice(0, 6)}`;
        }

        function setTenantViewTab(tab) {
            const detailsTab = document.getElementById('tenant-view-details-tab');
            const relatedTab = document.getElementById('tenant-view-customer-related-tab');
            const detailsContent = document.getElementById('tenant-view-content-details');
            const relatedContent = document.getElementById('tenant-view-content-customer-related');

            if (tab === 'related') {
                detailsTab.classList.remove('bg-white', 'text-blue-700');
                detailsTab.classList.add('bg-gray-100', 'text-gray-700');
                relatedTab.classList.add('bg-white', 'text-blue-700');
                relatedTab.classList.remove('bg-gray-100', 'text-gray-700');
                detailsContent.classList.add('hidden');
                relatedContent.classList.remove('hidden');
            } else {
                detailsTab.classList.add('bg-white', 'text-blue-700');
                detailsTab.classList.remove('bg-gray-100', 'text-gray-700');
                relatedTab.classList.remove('bg-white', 'text-blue-700');
                relatedTab.classList.add('bg-gray-100', 'text-gray-700');
                detailsContent.classList.remove('hidden');
                relatedContent.classList.add('hidden');
            }
        }

        function openTenantViewModal() {
            document.getElementById('tenant-view-modal').classList.remove('hidden');
            setTenantViewTab('details');
        }

        function openNewTenantModal() {
            document.getElementById('tenant-create-form').reset();
            clearTenantCreateErrors();
            const codeInput = document.getElementById('tenant-code');
            if (codeInput) {
                codeInput.value = generateTenantCode();
            }
            updateTenantDomainPreview();
            document.getElementById('tenant-create-modal').classList.remove('hidden');
        }

        function inviteOtherCustomer(tenantId, tenantName) {
            document.getElementById('tenant-invite-form').reset();
            document.getElementById('tenant-invite-tenant-id').value = tenantId;
            document.getElementById('tenant-invite-tenant-name').value = tenantName || '-';
            document.getElementById('tenant-invite-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function resetCreateTenantForm() {
            document.getElementById('tenant-create-form').reset();
            clearTenantCreateErrors();
            const codeInput = document.getElementById('tenant-code');
            if (codeInput) {
                codeInput.value = generateTenantCode();
            }
            updateTenantDomainPreview();
        }

        function updateTenantDomainPreview() {
            const domainInput = document.getElementById('tenant-domain');
            const preview = document.getElementById('tenant-domain-preview');

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

        function clearTenantCreateErrors() {
            const fields = ['code', 'name', 'domain', 'address', 'business-type'];

            fields.forEach(field => {
                const inputId = `tenant-${field}`;
                const errorId = `tenant-${field}-error`;
                const input = document.getElementById(inputId);
                const error = document.getElementById(errorId);

                if (input) {
                    input.classList.remove('border-red-400');
                }

                if (error) {
                    error.textContent = '';
                    error.classList.add('hidden');
                }
            });
        }

        function setTenantCreateFieldError(field, message) {
            const fieldMap = {
                code: 'code',
                name: 'name',
                domain: 'domain',
                address: 'address',
                business_type: 'business-type',
            };

            const key = fieldMap[field] || field;
            const input = document.getElementById(`tenant-${key}`);
            const error = document.getElementById(`tenant-${key}-error`);

            if (input) {
                input.classList.add('border-red-400');
            }

            if (error) {
                error.textContent = message;
                error.classList.remove('hidden');
            }
        }

        async function submitTenantInviteForm() {
            const tenantId = document.getElementById('tenant-invite-tenant-id').value;
            const email = document.getElementById('tenant-invite-email').value;
            const role = document.getElementById('tenant-invite-role').value;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('tenants') }}/${tenantId}/invite-customer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        email,
                        role,
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw result;
                }

                alert(result.message || 'Customer invited successfully.');
                closeModal('tenant-invite-modal');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to invite customer.');
            }
        }

        async function submitCreateTenantForm() {
            const form = document.getElementById('tenant-create-form');
            const formData = new FormData(form);
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            clearTenantCreateErrors();

            try {
                const response = await fetch('{{ route('tenants.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && result.errors) {
                        Object.entries(result.errors).forEach(([field, messages]) => {
                            const firstMessage = Array.isArray(messages) ? messages[0] : messages;
                            setTenantCreateFieldError(field, firstMessage);
                        });
                    }
                    throw result;
                }

                alert(result.message || 'Tenant created successfully.');
                closeModal('tenant-create-modal');
                resetCreateTenantForm();
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to create tenant.');
            }
        }

        document.getElementById('tenant-domain')?.addEventListener('input', function() {
            updateTenantDomainPreview();
        });

        function renderTenantView(data) {
            var tenant = data.tenant

            document.getElementById('tenant-view-code').textContent = tenant.code || '-';
            document.getElementById('tenant-view-name').textContent = tenant.user.name || '-';
            document.getElementById('tenant-view-email').textContent = tenant.user.email || '-';
            document.getElementById('tenant-view-phone').textContent = tenant.user.phone || '-';
            document.getElementById('tenant-view-avatar').textContent = tenant.user.avatar || '-';

            const relatedButton = document.getElementById('tenant-view-customer-related-tab');
            const tableWrapper = document.getElementById('tenant-view-customer-related-table-wrapper');
            const relatedEmpty = document.getElementById('tenant-view-customer-related-empty');
            const relatedList = document.getElementById('tenant-view-customer-related-list');

            if (Array.isArray(tenant.tenants) && tenant.tenants.length > 0) {
                relatedButton.classList.remove('hidden');
                relatedButton.disabled = false;
                relatedEmpty.classList.add('hidden');
                tableWrapper.classList.remove('hidden');

                relatedList.innerHTML = tenant.tenants.map(item => {
                    const code = item.code || '-';
                    const name = item.name || '-';
                    const type = (item.tenant_type && item.tenant_type.description) ? item.tenant_type
                        .description : (item.tenant_type || '-');

                    return `<tr class="border-t border-gray-100"><td class="px-3 py-2">${code}</td><td class="px-3 py-2">${name}</td><td class="px-3 py-2">${type}</td></tr>`;
                }).join('');
            } else {
                relatedButton.classList.add('hidden');
                relatedButton.disabled = true;
                relatedEmpty.classList.remove('hidden');
                tableWrapper.classList.add('hidden');
            }

            openTenantViewModal();
        }

        function viewTenant(tenantId) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('{{ url('tenants') }}/' + tenantId, {
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
                    renderTenantView(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load tenant data.');
                });
        }

        async function deleteTenant(tenantId, tenantName) {
            const confirmed = window.confirm(
                `Delete tenant "${tenantName}"? This action cannot be undone.`
            );

            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('tenants') }}/${tenantId}`, {
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

                alert(result.message || 'Tenant deleted successfully.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to delete tenant.');
            }
        }

        async function restoreTenant(tenantId, tenantName) {
            const confirmed = window.confirm(`Restore tenant "${tenantName}"?`);
            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('tenants') }}/${tenantId}/restore`, {
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

                alert(result.message || 'Tenant restored successfully.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to restore tenant.');
            }
        }

        async function permanentDeleteTenant(tenantId, tenantName) {
            const confirmed = window.confirm(
                `Permanently delete tenant "${tenantName}"? This cannot be undone and will remove all data.`
            );
            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('tenants') }}/${tenantId}/permanent-delete`, {
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

                alert(result.message || 'Tenant permanently deleted.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to permanently delete tenant.');
            }
        }

        document.getElementById('close-tenant-view-modal').addEventListener('click', function() {
            closeModal('tenant-view-modal');
        });

        document.getElementById('close-tenant-view-modal-cta').addEventListener('click', function() {
            closeModal('tenant-view-modal');
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal('tenant-view-modal');
                closeModal('tenant-create-modal');
                closeModal('tenant-invite-modal');
            }
        });
    </script>

@endsection
