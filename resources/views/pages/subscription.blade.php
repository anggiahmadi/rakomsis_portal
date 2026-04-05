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
            </div>

            @if ($subscriptions->count() > 0)
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
                                    Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($subscriptions as $subscription)
                                <tr class="{{ $subscription->trashed() ? 'bg-red-50 opacity-75' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if ($subscription->trashed())
                                                <button type="button"
                                                    onclick="restoreSubscription({{ $subscription->id }}, @js($subscription->user->name))"
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
                                                    onclick="permanentDeleteSubscription({{ $subscription->id }}, @js($subscription->user->name))"
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
                                                    onclick="deleteSubscription({{ $subscription->id }}, @js($subscription->user->name))"
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
                                        {{ $subscription->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="max-w-xs truncate {{ $subscription->trashed() ? 'line-through text-gray-400' : '' }}"
                                            title="{{ $subscription->user->name }}">
                                            {{ $subscription->user->name }}
                                        </div>
                                        @if ($subscription->trashed())
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 mt-1">
                                                Deleted {{ $subscription->deleted_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $subscription->user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $subscription->user->phone }}
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
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    Details
                </button>
                <button id="subscription-view-customer-related-tab" onclick="setSubscriptionViewTab('included')"
                    class="w-1/2 px-4 py-3 text-sm sm:text-base font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none hidden"
                    disabled>
                    Related Subscriptions
                </button>
            </div>

            <div id="subscription-view-content-details" class="p-6">
                <dl class="grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-2 text-sm text-gray-700">
                    <div>
                        <dt class="font-semibold">Code</dt>
                        <dd id="subscription-view-code">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Name</dt>
                        <dd id="subscription-view-name">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Domain</dt>
                        <dd id="subscription-view-domain">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Address</dt>
                        <dd id="subscription-view-address">-</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Business Type</dt>
                        <dd id="subscription-view-business-type">-</dd>
                    </div>
                </dl>
            </div>

            <div id="subscription-view-content-customer-related" class="hidden p-6">
                <p id="subscription-view-customer-related-empty" class="text-sm text-gray-500">No related subscriptions
                    are available.
                </p>
                <div class="mt-4 overflow-x-auto hidden" id="subscription-view-customer-related-table-wrapper">
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
                        <tbody id="subscription-view-customer-related-list"></tbody>
                    </table>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 text-right">
                <button id="close-subscription-view-modal-cta" onclick="closeModal('subscription-view-modal')"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Close</button>
            </div>
        </div>
    </div>

    <script>
        function setSubscriptionViewTab(tab) {
            const detailsTab = document.getElementById('subscription-view-details-tab');
            const relatedTab = document.getElementById('subscription-view-customer-related-tab');
            const detailsContent = document.getElementById('subscription-view-content-details');
            const relatedContent = document.getElementById('subscription-view-content-customer-related');

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

        function openSubscriptionViewModal() {
            document.getElementById('subscription-view-modal').classList.remove('hidden');
            setSubscriptionViewTab('details');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function renderSubscriptionView(data) {
            var subscription = data.subscription

            document.getElementById('subscription-view-code').textContent = subscription.code || '-';
            document.getElementById('subscription-view-name').textContent = subscription.user.name || '-';
            document.getElementById('subscription-view-email').textContent = subscription.user.email || '-';
            document.getElementById('subscription-view-phone').textContent = subscription.user.phone || '-';
            document.getElementById('subscription-view-avatar').textContent = subscription.user.avatar || '-';

            const relatedButton = document.getElementById('subscription-view-customer-related-tab');
            const tableWrapper = document.getElementById('subscription-view-customer-related-table-wrapper');
            const relatedEmpty = document.getElementById('subscription-view-customer-related-empty');
            const relatedList = document.getElementById('subscription-view-customer-related-list');

            if (Array.isArray(subscription.subscriptions) && subscription.subscriptions.length > 0) {
                relatedButton.classList.remove('hidden');
                relatedButton.disabled = false;
                relatedEmpty.classList.add('hidden');
                tableWrapper.classList.remove('hidden');

                relatedList.innerHTML = subscription.subscriptions.map(item => {
                    const code = item.code || '-';
                    const name = item.name || '-';
                    const type = (item.subscription_type && item.subscription_type.description) ? item
                        .subscription_type
                        .description : (item.subscription_type || '-');

                    return `<tr class="border-t border-gray-100"><td class="px-3 py-2">${code}</td><td class="px-3 py-2">${name}</td><td class="px-3 py-2">${type}</td></tr>`;
                }).join('');
            } else {
                relatedButton.classList.add('hidden');
                relatedButton.disabled = true;
                relatedEmpty.classList.remove('hidden');
                tableWrapper.classList.add('hidden');
            }

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

        async function deleteSubscription(subscriptionId, subscriptionName) {
            const confirmed = window.confirm(
                `Delete subscription "${subscriptionName}"? This action cannot be undone.`
            );

            if (!confirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(`{{ url('subscriptions') }}/${subscriptionId}`, {
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

                alert(result.message || 'Subscription deleted successfully.');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to delete subscription.');
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

        document.getElementById('close-subscription-view-modal-cta').addEventListener('click', function() {
            closeModal('subscription-view-modal');
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal('subscription-view-modal');
            }
        });
    </script>

@endsection
