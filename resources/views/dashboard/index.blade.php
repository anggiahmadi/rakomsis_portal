@extends('components.layout.main')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Header -->
        <div class="bg-gradient-to-r from-[#034c8f] to-[#00a8e3] rounded-lg shadow-lg p-6 text-white">
            <h1 class="text-3xl font-bold mb-2">Welcome, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-blue-100">Manage your profile and account settings from here</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Grid 1: Active Tenants -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4" style="border-left-color: #034c8f;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Tenants</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            @if ($tenants_count > 0)
                                {{ $tenants_count }}
                            @else
                                <button type="button" id="trial-tenants-btn" class="px-3 py-1 text-sm rounded text-white"
                                    style="background-color: #034c8f;">
                                    Try now !!!
                                </button>
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#034c8f]" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.5 1.5H3.75A2.25 2.25 0 001.5 3.75v12.5A2.25 2.25 0 003.75 18.5h12.5a2.25 2.25 0 002.25-2.25V9.5M18.5 1.5l-7 7m0 0l2-2m-2 2l-2-2">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Grid 2: Subscription Status -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4" style="border-left-color: #00a8e3;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Subscription</p>
                        <p class="text-1xl font-bold text-gray-900 mt-1">
                            @if ($total_subscription > 0)
                                <a href="{{ route('subscriptions.index') }}">{{ $total_subscription }} Active
                                    Subscription(s)</a>
                            @else
                                <button type="button" id="trial-subscription-btn"
                                    class="px-3 py-1 text-sm rounded text-white" style="background-color: #00a8e3;">
                                    Try now !!!
                                </button>
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00a8e3]" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Grid 3: Total Agent Commission -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4" style="border-left-color: #7a8b9c;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Agent Commission</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            @if ($agent)
                                Rp {{ number_format($agent->total_commission, 0, ',', '.') }}
                            @else
                                <button type="button" id="agent-commission-btn"
                                    class="px-3 py-1 text-sm rounded text-white" style="background-color: #7a8b9c;">
                                    Became an agent
                                </button>
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M8.433 7.418c.155.03.299.112.358.278.06.166.047.37-.128.528-.637.635-1.876 1.038-3.708 1.038-.468 0-.944-.054-1.387-.172a2 2 0 00-.242 3.966c.857.143 1.8.194 2.75.194 1.933 0 3.735-.645 4.59-1.574.56-.54.588-1.472.014-2.04a.694.694 0 00-.havoc.118zm-1.88-1.637a4.5 4.5 0 00-1.516.294.75.75 0 11-.502-1.422A5.99 5.99 0 018 2a6 6 0 012.75 11.448.75.75 0 11-.75-1.299A4.5 4.5 0 108 2.75z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Grid 4: Agent's Balance -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4" style="border-left-color: #323F4C;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Agent's Balance</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            @if ($agent)
                                Rp {{ number_format($agent->balance, 0, ',', '.') }}
                            @else
                                <button type="button" id="agent-balance-btn" class="px-3 py-1 text-sm rounded text-white"
                                    style="background-color: #323F4C;">
                                    Became an agent
                                </button>
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('dashboard.profile') }}"
                    class="p-4 border border-gray-200 rounded-lg hover:bg-blue-50 transition flex items-center justify-between group">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200">
                            <svg class="w-5 h-5 text-[#034c8f]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Edit Profile</p>
                            <p class="text-sm text-gray-600">Update your personal information</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-[#034c8f]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>

                <a href="{{ route('dashboard.settings') }}"
                    class="p-4 border border-gray-200 rounded-lg hover:bg-cyan-50 transition flex items-center justify-between group">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-cyan-200">
                            <svg class="w-5 h-5 text-[#00a8e3]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.533 1.533 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.533 1.533 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Account Settings</p>
                            <p class="text-sm text-gray-600">Manage password and security</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-[#00a8e3]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- User Information -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Account Information</h2>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Full Name</span>
                    <span class="font-medium text-gray-900">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Email Address</span>
                    <span class="font-medium text-gray-900">{{ Auth::user()->email }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Account Created</span>
                    <span class="font-medium text-gray-900">{{ Auth::user()->created_at->format('F d, Y') }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600">Last Updated</span>
                    <span
                        class="font-medium text-gray-900">{{ Auth::user()->updated_at->format('F d, Y \a\t h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Trial Modal (Tenants & Subscription) -->
    <div id="trial-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8 hidden">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Free Trial - 1 Month SaaS Access</h3>
                <button id="close-trial-modal" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>
            <form method="POST" action="{{ route('dashboard.trial.start') }}" id="trial-form">
                @csrf
                <input type="hidden" name="trial_target" id="trial-target" value="tenants">

                <div class="p-6 text-sm text-gray-700 space-y-4">
                    <div class="rounded-lg bg-blue-50 border border-blue-100 p-4 text-blue-900">
                        <p class="font-semibold">Free Trial</p>
                        <p class="mt-1 text-sm text-blue-800">Start a 1-month trial by choosing a tenant, product, and
                            start date.</p>
                    </div>

                    @if ($customer && $customerTenants->isNotEmpty())
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Tenant Option</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label
                                    class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-3 cursor-pointer">
                                    <input type="radio" name="tenant_mode" value="existing" checked
                                        class="text-[#034c8f] focus:ring-[#034c8f]">
                                    <span>Use Existing Tenant</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-3 cursor-pointer">
                                    <input type="radio" name="tenant_mode" value="new"
                                        class="text-[#034c8f] focus:ring-[#034c8f]">
                                    <span>Create New Tenant</span>
                                </label>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="tenant_mode" value="new">
                        <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-amber-800">
                            You do not have a tenant yet. Create one first to start your free trial.
                        </div>
                    @endif

                    <div id="trial-existing-tenant-section"
                        class="space-y-2 {{ !$customer || $customerTenants->isEmpty() ? 'hidden' : '' }}">
                        <label for="trial-tenant-id" class="block text-sm font-semibold text-gray-800">Select
                            Tenant</label>
                        <select name="tenant_id" id="trial-tenant-id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Choose your tenant</option>
                            @foreach ($customerTenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->domain }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="trial-new-tenant-section"
                        class="space-y-4 {{ $customer && $customerTenants->isNotEmpty() ? 'hidden' : '' }}">
                        <div>
                            <label for="trial-tenant-name" class="block text-sm font-semibold text-gray-800 mb-1">Tenant
                                Name</label>
                            <input type="text" name="tenant_name" id="trial-tenant-name"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="My School / My Business">
                        </div>
                        <div>
                            <label for="trial-tenant-domain" class="block text-sm font-semibold text-gray-800 mb-1">Tenant
                                Domain</label>
                            <input type="text" name="tenant_domain" id="trial-tenant-domain"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="my-tenant.rakomsis.com">
                        </div>
                        <div>
                            <label for="trial-tenant-address"
                                class="block text-sm font-semibold text-gray-800 mb-1">Address</label>
                            <input type="text" name="tenant_address" id="trial-tenant-address"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tenant address">
                        </div>
                        <div>
                            <label for="trial-tenant-business-type"
                                class="block text-sm font-semibold text-gray-800 mb-1">Business Type</label>
                            <input type="text" name="tenant_business_type" id="trial-tenant-business-type"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="School, Retail, Services, etc.">
                        </div>
                    </div>

                    <div>
                        <label for="trial-product-id" class="block text-sm font-semibold text-gray-800 mb-1">Select
                            Product for Trial</label>
                        <input type="text" id="trial-product-search"
                            placeholder="Search bundle products by name or billing cycle..."
                            class="w-full mb-2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <select name="product_id" id="trial-product-id" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Choose a bundle product</option>
                            @foreach ($trialProducts as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }} - {{ $product->billing_cycle->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="trial-start-date" class="block text-sm font-semibold text-gray-800 mb-1">Trial Start
                            Date</label>
                        <input type="date" name="start_date" id="trial-start-date" min="{{ now()->toDateString() }}"
                            required value="{{ now()->toDateString() }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
                    <button type="button" id="cancel-trial-modal"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">Cancel</button>
                    <button type="submit" id="continue-trial-modal"
                        class="px-4 py-2 rounded-lg text-white font-semibold" style="background-color: #034c8f;">Start
                        Free Trial</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Agent Modal (Commission & Balance) -->
    <div id="agent-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8 hidden">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-lg overflow-hidden max-h-96 overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-900">Become an Agent - Benefits Breakdown</h3>
                <button id="close-agent-modal" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>
            <div class="p-6 text-sm text-gray-700 space-y-4">
                <!-- Bronze Level -->
                <div class="border-l-4 pl-4" style="border-left-color: #CD7F32;">
                    <p class="font-semibold text-gray-900">🥉 Bronze Level (Start Here)</p>
                    <p class="text-gray-600 mt-1">Entry-level agent membership available to all users.</p>
                    <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                        <li><strong>Commission Rate:</strong> 2% on all referred sales</li>
                        <li><strong>Customer Discount:</strong> 5% discount for your referrals</li>
                        <li><strong>Support:</strong> Basic support and promotional materials</li>
                        <li><strong>Requirement:</strong> No sales threshold required</li>
                    </ul>
                </div>

                <!-- Silver Level -->
                <div class="border-l-4 pl-4" style="border-left-color: #C0C0C0;">
                    <p class="font-semibold text-gray-900">🥈 Silver Level</p>
                    <p class="text-gray-600 mt-1">Upgrade after achieving IDR 100,000,000 in sales.</p>
                    <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                        <li><strong>Commission Rate:</strong> 5% on all referred sales</li>
                        <li><strong>Customer Discount:</strong> 10% discount for your referrals</li>
                        <li><strong>Support:</strong> Priority support + exclusive promotions</li>
                        <li><strong>Requirement:</strong> IDR 100,000,000 total sales</li>
                    </ul>
                </div>

                <!-- Gold Level -->
                <div class="border-l-4 pl-4" style="border-left-color: #FFD700;">
                    <p class="font-semibold text-gray-900">🥇 Gold Level</p>
                    <p class="text-gray-600 mt-1">Premium agent status with IDR 1,000,000,000+ in sales.</p>
                    <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                        <li><strong>Commission Rate:</strong> 10% on all referred sales</li>
                        <li><strong>Customer Discount:</strong> 15% discount for your referrals</li>
                        <li><strong>Support:</strong> Dedicated account management + early access</li>
                        <li><strong>Requirement:</strong> IDR 1,000,000,000 total sales</li>
                    </ul>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex gap-3 justify-end sticky bottom-0 bg-white">
                <button id="cancel-agent-modal"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">Cancel</button>
                <button id="continue-agent-modal" class="px-4 py-2 rounded-lg text-white font-semibold"
                    style="background-color: #034c8f;">Continue to be an Agent</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Agent -->
    <div id="agent-confirmation-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8 hidden">
        <div class="bg-white rounded-xl w-full max-w-md shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Confirm Agent Registration</h3>
            </div>
            <div class="p-6 text-sm text-gray-700 space-y-4">
                <p>Are you sure you want to become an agent? You will start at the <strong>Bronze level</strong> with:</p>
                <ul class="list-disc pl-5 space-y-1 text-gray-600">
                    <li>2% commission on all referred sales</li>
                    <li>5% customer discount</li>
                    <li>Basic support and promotional materials</li>
                </ul>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
                <button id="cancel-confirmation"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">Cancel</button>
                <button id="confirm-agent" class="px-4 py-2 rounded-lg text-white font-semibold"
                    style="background-color: #034c8f;">Yes, Proceed</button>
            </div>
        </div>
    </div>

    <script>
        // Trial Modal Handlers
        function openTrialModal(target) {
            document.getElementById('trial-target').value = target;
            const productSearch = document.getElementById('trial-product-search');
            if (productSearch) {
                productSearch.value = '';
            }
            filterTrialProductOptions();
            document.getElementById('trial-modal').classList.remove('hidden');
        }

        function filterTrialProductOptions() {
            const searchInput = document.getElementById('trial-product-search');
            const select = document.getElementById('trial-product-id');

            if (!searchInput || !select) {
                return;
            }

            const keyword = searchInput.value.trim().toLowerCase();
            Array.from(select.options).forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                option.hidden = keyword !== '' && !option.text.toLowerCase().includes(keyword);
            });

            if (select.selectedOptions.length > 0 && select.selectedOptions[0].hidden) {
                select.value = '';
            }
        }

        document.getElementById('trial-tenants-btn')?.addEventListener('click', function() {
            openTrialModal('tenants');
        });

        document.getElementById('trial-subscription-btn')?.addEventListener('click', function() {
            openTrialModal('subscription');
        });

        document.getElementById('close-trial-modal').addEventListener('click', function() {
            document.getElementById('trial-modal').classList.add('hidden');
        });

        document.getElementById('cancel-trial-modal').addEventListener('click', function() {
            document.getElementById('trial-modal').classList.add('hidden');
        });

        document.getElementById('trial-product-search').addEventListener('input', function() {
            filterTrialProductOptions();
        });

        function syncTrialTenantMode() {
            const selectedMode = document.querySelector('input[name="tenant_mode"]:checked')?.value || 'new';
            const existingSection = document.getElementById('trial-existing-tenant-section');
            const newSection = document.getElementById('trial-new-tenant-section');
            const existingSelect = document.getElementById('trial-tenant-id');
            const newInputs = [
                document.getElementById('trial-tenant-name'),
                document.getElementById('trial-tenant-domain'),
                document.getElementById('trial-tenant-address'),
                document.getElementById('trial-tenant-business-type'),
            ];

            if (existingSection) {
                existingSection.classList.toggle('hidden', selectedMode !== 'existing');
            }

            if (newSection) {
                newSection.classList.toggle('hidden', selectedMode !== 'new');
            }

            if (existingSelect) {
                existingSelect.disabled = selectedMode !== 'existing';
            }

            newInputs.forEach(input => {
                if (input) {
                    input.disabled = selectedMode !== 'new';
                }
            });
        }

        document.querySelectorAll('input[name="tenant_mode"]').forEach(radio => {
            radio.addEventListener('change', syncTrialTenantMode);
        });

        syncTrialTenantMode();

        // Agent Modal Handlers
        document.getElementById('agent-commission-btn')?.addEventListener('click', function() {
            document.getElementById('agent-modal').classList.remove('hidden');
        });

        document.getElementById('agent-balance-btn')?.addEventListener('click', function() {
            document.getElementById('agent-modal').classList.remove('hidden');
        });

        document.getElementById('close-agent-modal').addEventListener('click', function() {
            document.getElementById('agent-modal').classList.add('hidden');
        });

        document.getElementById('cancel-agent-modal').addEventListener('click', function() {
            document.getElementById('agent-modal').classList.add('hidden');
        });

        document.getElementById('continue-agent-modal').addEventListener('click', function() {
            document.getElementById('agent-modal').classList.add('hidden');
            document.getElementById('agent-confirmation-modal').classList.remove('hidden');
        });

        document.getElementById('cancel-confirmation').addEventListener('click', function() {
            document.getElementById('agent-confirmation-modal').classList.add('hidden');
        });

        document.getElementById('confirm-agent').addEventListener('click', function() {
            // Get CSRF token from meta tag
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Disable button to prevent double submission
            this.disabled = true;
            const originalText = this.textContent;
            this.textContent = 'Processing...';

            // Make AJAX POST request to agent.store route
            fetch('{{ route('agent.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        level: 'bronze' // Starting level is always bronze
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => Promise.reject(data));
                    }
                    return response.json();
                })
                .then(data => {
                    // Close modal
                    document.getElementById('agent-confirmation-modal').classList.add('hidden');

                    // Show success message
                    alert('Congratulations! You are now a Bronze agent.');

                    // Reload page to reflect agent status
                    // window.location.reload();
                    window.location.href = "{{ route('dashboard.agent') }}";
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error becoming an agent. Please try again.');

                    // Re-enable button
                    this.disabled = false;
                    this.textContent = originalText;
                });
        });
    </script>
@endsection
