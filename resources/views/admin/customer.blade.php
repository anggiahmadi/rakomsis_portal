@extends('components.layout.main')

@section('title', 'Customer Management')

@section('content')
    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('customers.index') }}" class="flex gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Customers</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Search by name or email..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>


                <div class="flex items-end gap-2">
                    <label class="flex items-center gap-2 pb-2 cursor-pointer select-none">
                        <input type="checkbox" name="is_agent" value="1" {{ $isAgent ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm font-medium text-gray-700">Show Agents</span>
                    </label>
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
                    @if (request('search') || $showDeleted || $isAgent)
                        <a href="{{ route('customers.index') }}"
                            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Customers Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $showDeleted ? 'Deleted Customers' : 'Customers' }} ({{ $customers->total() }})
                </h3>
            </div>

            @if ($customers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    is Agent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($customers as $customer)
                                @php
                                    $canDeleteByAge =
                                        $customer->created_at && $customer->created_at->lte(now()->subDays(30));
                                @endphp
                                <tr class="{{ $customer->trashed() ? 'bg-red-50 opacity-75' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if ($customer->trashed())
                                                <button type="button"
                                                    onclick="restoreCustomer({{ $customer->id }}, @js($customer->user->name))"
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
                                                @if ($canDeleteByAge)
                                                    <button type="button"
                                                        onclick="permanentDeleteCustomer({{ $customer->id }}, @js($customer->user->name))"
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
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium text-amber-700 bg-amber-100">
                                                        Delete available after
                                                        {{ optional($customer->created_at)->addDays(30)->format('M d, Y') }}
                                                    </span>
                                                @endif
                                            @else
                                                <button type="button" onclick="viewCustomer({{ $customer->id }})"
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
                                                @if ($canDeleteByAge)
                                                    <button type="button"
                                                        onclick="deleteCustomer({{ $customer->id }}, @js($customer->user->name))"
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
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium text-amber-700 bg-amber-100">
                                                        Delete available after
                                                        {{ optional($customer->created_at)->addDays(30)->format('M d, Y') }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $customer->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $customer->user->agent ? 'Yes' : 'No' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="max-w-xs truncate {{ $customer->trashed() ? 'line-through text-gray-400' : '' }}"
                                            title="{{ $customer->user->name }}">
                                            {{ $customer->user->name }}
                                        </div>
                                        @if ($customer->trashed())
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 mt-1">
                                                Deleted {{ $customer->deleted_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $customer->user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $customer->user->phone }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $customer->created_at->format('M d, Y H:i') }}
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
                            Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of
                            {{ $customers->total() }} results
                        </div>
                        <div class="flex-1 flex justify-end">
                            {{ $customers->appends(request()->query())->links('vendor.pagination.tailwind') }}
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No customers found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if (request('search'))
                            No customers match your search criteria.
                        @else
                            Get started by creating your first customer.
                        @endif
                    </p>
                    @if (request('search'))
                        <div class="mt-6">
                            <a href="{{ route('customers.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Clear Search
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- View Customer Modal -->
    <div id="customer-view-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 sm:p-6 hidden">
        <div
            class="bg-white rounded-xl w-full max-w-xl sm:max-w-2xl lg:max-w-3xl shadow-2xl overflow-hidden border border-blue-700/20 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="px-5 py-3 bg-blue-700 text-white flex items-center justify-between">
                <h3 id="customer-view-modal-title" class="text-base sm:text-lg font-bold">Customer Details</h3>
                <button id="close-customer-view-modal" class="text-white hover:text-slate-200">✕</button>
            </div>

            <div class="flex border-b border-blue-100 bg-blue-50">
                <button id="customer-view-details-tab" onclick="setCustomerViewTab('details')"
                    class="w-1/2 px-4 py-3 text-sm sm:text-base font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none">
                    Details
                </button>
                <button id="customer-view-tenant-related-tab" onclick="setCustomerViewTab('included')"
                    class="w-1/2 px-4 py-3 text-sm sm:text-base font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none hidden"
                    disabled>
                    Related Tenants
                </button>
            </div>

            <div id="customer-view-content-details" class="p-6">
                <dl class="grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-2 text-sm text-gray-700">
                    <div>
                        <dt class="font-semibold">Code</dt>
                        <dd id="customer-view-code">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Name</dt>
                        <dd id="customer-view-name">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Email</dt>
                        <dd id="customer-view-email">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Phone</dt>
                        <dd id="customer-view-phone">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Avatar</dt>
                        <dd id="customer-view-avatar">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Is Agent</dt>
                        <dd id="customer-view-is-agent">-</dd>
                    </div>
                </dl>
                <br>
                <hr><br>
                <dl class="grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-2 text-sm text-gray-700 hidden"
                    id="agent-list-data">
                    <div>
                        <dt class="font-semibold">Bank Name</dt>
                        <dd id="agent-view-bank-name">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Bank Account</dt>
                        <dd id="agent-view-bank-account">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Level</dt>
                        <dd id="agent-view-level">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Balance</dt>
                        <dd id="agent-view-balance">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Commision Rate</dt>
                        <dd id="agent-view-commission-rate">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Discount Rate</dt>
                        <dd id="agent-view-discount-rate">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Total Sales</dt>
                        <dd id="agent-view-total-sales">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Total Commision</dt>
                        <dd id="agent-view-total-commission">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Withdrawn</dt>
                        <dd id="agent-view-withdrawn">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Pending Withdrawal</dt>
                        <dd id="agent-view-pending-withdrawal">-</dd>
                    </div>
                </dl>
            </div>

            <div id="customer-view-content-tenant-related" class="hidden p-6">
                <p id="customer-view-tenant-related-empty" class="text-sm text-gray-500">No related tenants are available.
                </p>
                <div class="mt-4 overflow-x-auto hidden" id="customer-view-tenant-related-table-wrapper">
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
                        <tbody id="customer-view-tenant-related-list"></tbody>
                    </table>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 text-right">
                <button id="close-customer-view-modal-cta" onclick="closeModal('customer-view-modal')"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Close</button>
            </div>
        </div>
    </div>

    <script>
        function setCustomerViewTab(tab) {
            const detailsTab = document.getElementById('customer-view-details-tab');
            const relatedTab = document.getElementById('customer-view-tenant-related-tab');
            const detailsContent = document.getElementById('customer-view-content-details');
            const relatedContent = document.getElementById('customer-view-content-tenant-related');

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

        function openCustomerViewModal() {
            document.getElementById('customer-view-modal').classList.remove('hidden');
            setCustomerViewTab('details');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function renderCustomerView(data) {
            var customer = data.customer

            document.getElementById('customer-view-code').textContent = customer.code || '-';
            document.getElementById('customer-view-name').textContent = customer.user.name || '-';
            document.getElementById('customer-view-email').textContent = customer.user.email || '-';
            document.getElementById('customer-view-phone').textContent = customer.user.phone || '-';
            document.getElementById('customer-view-avatar').textContent = customer.user.avatar || '-';
            document.getElementById('customer-view-is-agent').textContent = customer.user.agent ? 'Yes' : 'No';

            const relatedButton = document.getElementById('customer-view-tenant-related-tab');
            const tableWrapper = document.getElementById('customer-view-tenant-related-table-wrapper');
            const relatedEmpty = document.getElementById('customer-view-tenant-related-empty');
            const relatedList = document.getElementById('customer-view-tenant-related-list');
            const agentDataSection = document.getElementById('agent-list-data');

            if (customer.user.agent) {
                agentDataSection.classList.remove('hidden');
                document.getElementById('agent-view-bank-name').textContent = customer.user.agent.bank_name || '-';
                document.getElementById('agent-view-bank-account').textContent = customer.user.agent.bank_account || '-';
                document.getElementById('agent-view-level').textContent = customer.user.agent.level || '-';
                document.getElementById('agent-view-balance').textContent = customer.user.agent.balance ?? '-';
                document.getElementById('agent-view-commission-rate').textContent = customer.user.agent.commission_rate ? (
                    customer.user.agent.commission_rate + '%') : '-';
                document.getElementById('agent-view-discount-rate').textContent = customer.user.agent.discount_rate ? (
                    customer.user.agent.discount_rate + '%') : '-';
                document.getElementById('agent-view-total-sales').textContent = customer.user.agent.total_sales ?? '-';
                document.getElementById('agent-view-total-commission').textContent = customer.user.agent.total_commission ??
                    '-';
                document.getElementById('agent-view-withdrawn').textContent = customer.user.agent.withdrawn ?? '-';
                document.getElementById('agent-view-pending-withdrawal').textContent = customer.user.agent
                    .pending_withdrawal ?? '-';
            } else {
                agentDataSection.classList.add('hidden');
            }

            if (Array.isArray(customer.tenants) && customer.tenants.length > 0) {
                relatedButton.classList.remove('hidden');
                relatedButton.disabled = false;
                relatedEmpty.classList.add('hidden');
                tableWrapper.classList.remove('hidden');

                relatedList.innerHTML = customer.tenants.map(item => {
                    const code = item.code || '-';
                    const name = item.name || '-';
                    const type = (item.customer_type && item.customer_type.description) ? item.customer_type
                        .description : (item.customer_type || '-');

                    return `<tr class="border-t border-gray-100"><td class="px-3 py-2">${code}</td><td class="px-3 py-2">${name}</td><td class="px-3 py-2">${type}</td></tr>`;
                }).join('');
            } else {
                relatedButton.classList.add('hidden');
                relatedButton.disabled = true;
                relatedEmpty.classList.remove('hidden');
                tableWrapper.classList.add('hidden');
            }

            openCustomerViewModal();
        }

        function viewCustomer(customerId) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('{{ url('customers') }}/' + customerId, {
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
                    renderCustomerView(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load customer data.');
                });
        }

        async function deleteCustomer(customerId, customerName) {
            const confirmed = window.confirm(
                `Delete customer "${customerName}"? This action cannot be undone.`
            );

            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('customers') }}/${customerId}`, {
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

                alert(result.message || 'Customer deleted successfully.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to delete customer.');
            }
        }

        async function restoreCustomer(customerId, customerName) {
            const confirmed = window.confirm(`Restore customer "${customerName}"?`);
            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('customers') }}/${customerId}/restore`, {
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

                alert(result.message || 'Customer restored successfully.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to restore customer.');
            }
        }

        async function permanentDeleteCustomer(customerId, customerName) {
            const confirmed = window.confirm(
                `Permanently delete customer "${customerName}"? This cannot be undone and will remove all data.`
            );
            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('customers') }}/${customerId}/permanent-delete`, {
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

                alert(result.message || 'Customer permanently deleted.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to permanently delete customer.');
            }
        }

        document.getElementById('close-customer-view-modal').addEventListener('click', function() {
            closeModal('customer-view-modal');
        });

        document.getElementById('close-customer-view-modal-cta').addEventListener('click', function() {
            closeModal('customer-view-modal');
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal('customer-view-modal');
            }
        });
    </script>

@endsection
