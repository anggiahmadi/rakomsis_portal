@extends('components.layout.main')

@section('title', 'Free Trial')

@section('content')
    @php
        $trialErrors = $errors->getBag('trial');
        $selectedTenantMode = old('tenant_mode', $customer && $customerTenants->isNotEmpty() ? 'existing' : 'new');
    @endphp

    <div class="space-y-6">
        <!-- Free Trial Header -->
        <div class="bg-gradient-to-r from-[#034c8f] to-[#00a8e3] rounded-lg shadow-lg p-6 text-white">
            <h1 class="text-3xl font-bold mb-2">Welcome, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-blue-100">Start your free trial and explore our features.</p>
        </div>

        <!-- Form Card Style (Similar to Profile/Settings) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Free Trial Information</h2>

            <div class="rounded-lg bg-blue-50 border border-blue-100 p-4 text-blue-900 text-sm">
                Trial duration is 1 month and only bundle products are eligible for this free trial.
            </div>

            <form method="POST" action="{{ route('dashboard.trial.start') }}" id="trial-form" class="space-y-6">
                @csrf
                <input type="hidden" name="trial_target" id="trial-target" value="tenants">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tenant Option</label>
                    @if ($customer && $customerTenants->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label
                                class="flex items-center gap-2 border border-gray-200 rounded-lg px-4 py-2.5 cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                <input type="radio" name="tenant_mode" value="existing"
                                    {{ $selectedTenantMode === 'existing' ? 'checked' : '' }}
                                    class="text-[#034c8f] focus:ring-[#034c8f]">
                                <span>Use Existing Tenant</span>
                            </label>
                            <label
                                class="flex items-center gap-2 border border-gray-200 rounded-lg px-4 py-2.5 cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                <input type="radio" name="tenant_mode" value="new"
                                    {{ $selectedTenantMode === 'new' ? 'checked' : '' }}
                                    class="text-[#034c8f] focus:ring-[#034c8f]">
                                <span>Create New Tenant</span>
                            </label>
                        </div>
                        @if ($trialErrors->has('tenant_mode'))
                            <p class="mt-1 text-sm text-red-600">{{ $trialErrors->first('tenant_mode') }}</p>
                        @endif
                    @else
                        <input type="hidden" name="tenant_mode" value="new">
                        <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-amber-800 text-sm">
                            You do not have any tenant yet, so trial will create a new tenant automatically.
                        </div>
                    @endif
                </div>

                <div id="trial-existing-tenant-section"
                    class="space-y-2 {{ !$customer || $customerTenants->isEmpty() || $selectedTenantMode !== 'existing' ? 'hidden' : '' }}">
                    <label for="trial-tenant-id" class="block text-sm font-medium text-gray-700">Select Existing
                        Tenant</label>
                    <select name="tenant_id" id="trial-tenant-id"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition {{ $trialErrors->has('tenant_id') ? 'border-red-500' : 'border-gray-300' }}">
                        <option value="">Choose your tenant</option>
                        @foreach ($customerTenants as $tenant)
                            <option value="{{ $tenant->id }}" @selected(old('tenant_id') == $tenant->id)>{{ $tenant->name }}
                                ({{ $tenant->domain }})
                            </option>
                        @endforeach
                    </select>
                    @if ($trialErrors->has('tenant_id'))
                        <p class="text-sm text-red-600">{{ $trialErrors->first('tenant_id') }}</p>
                    @endif
                </div>

                <div id="trial-new-tenant-section"
                    class="space-y-4 {{ $customer && $customerTenants->isNotEmpty() && $selectedTenantMode !== 'new' ? 'hidden' : '' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="trial-tenant-name" class="block text-sm font-medium text-gray-700 mb-2">Tenant
                                Name</label>
                            <input type="text" name="tenant_name" id="trial-tenant-name"
                                value="{{ old('tenant_name') }}"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition {{ $trialErrors->has('tenant_name') ? 'border-red-500' : 'border-gray-300' }}"
                                placeholder="My School / My Business">
                            @if ($trialErrors->has('tenant_name'))
                                <p class="mt-1 text-sm text-red-600">{{ $trialErrors->first('tenant_name') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="trial-tenant-domain" class="block text-sm font-medium text-gray-700 mb-2">Tenant
                                Domain</label>
                            @php
                                $oldDomainPrefix = old('tenant_domain');
                                if (is_string($oldDomainPrefix) && str_ends_with($oldDomainPrefix, '.rakomsis.com')) {
                                    $oldDomainPrefix = substr($oldDomainPrefix, 0, -strlen('.rakomsis.com'));
                                }
                            @endphp
                            <div
                                class="flex items-center rounded-lg border {{ $trialErrors->has('tenant_domain') ? 'border-red-500' : 'border-gray-300' }} focus-within:ring-2 focus-within:ring-[#034c8f] focus-within:border-transparent transition">
                                <input type="text" name="tenant_domain" id="trial-tenant-domain"
                                    value="{{ $oldDomainPrefix }}"
                                    class="w-full border-0 rounded-l-lg px-4 py-2.5 focus:ring-0" placeholder="your-domain"
                                    autocomplete="off">
                                <span
                                    class="px-3 text-sm text-gray-500 border-l border-gray-200 py-2.5">.rakomsis.com</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Preview: <span
                                    id="trial-domain-preview">your-domain.rakomsis.com</span></p>
                            @if ($trialErrors->has('tenant_domain'))
                                <p class="mt-1 text-sm text-red-600">{{ $trialErrors->first('tenant_domain') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="trial-tenant-address"
                                class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="tenant_address" id="trial-tenant-address"
                                value="{{ old('tenant_address') }}"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition {{ $trialErrors->has('tenant_address') ? 'border-red-500' : 'border-gray-300' }}"
                                placeholder="Tenant address">
                            @if ($trialErrors->has('tenant_address'))
                                <p class="mt-1 text-sm text-red-600">{{ $trialErrors->first('tenant_address') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="trial-tenant-business-type"
                                class="block text-sm font-medium text-gray-700 mb-2">Business Type</label>
                            <input type="text" name="tenant_business_type" id="trial-tenant-business-type"
                                value="{{ old('tenant_business_type') }}"
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition {{ $trialErrors->has('tenant_business_type') ? 'border-red-500' : 'border-gray-300' }}"
                                placeholder="School, Retail, Services, etc.">
                            @if ($trialErrors->has('tenant_business_type'))
                                <p class="mt-1 text-sm text-red-600">{{ $trialErrors->first('tenant_business_type') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="trial-product-id" class="block text-sm font-medium text-gray-700 mb-2">
                            Product
                        </label>
                        <input type="text" id="trial-product-search" placeholder="Search products..."
                            class="w-full mb-2 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition">
                        <select name="product_id" id="trial-product-id" required
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition {{ $trialErrors->has('product_id') ? 'border-red-500' : 'border-gray-300' }}">
                            <option value="">Choose a product</option>
                            @foreach ($trialProducts as $product)
                                <option value="{{ $product->id }}" @selected((string) old('product_id') === (string) $product->id)>
                                    {{ $product->name }} - {{ $product->billing_cycle->label() }}
                                </option>
                            @endforeach
                        </select>
                        @if ($trialErrors->has('product_id'))
                            <p class="mt-1 text-sm text-red-600">{{ $trialErrors->first('product_id') }}</p>
                        @endif
                    </div>
                    <div>
                        <label for="trial-start-date" class="block text-sm font-medium text-gray-700 mb-2">Trial Start
                            Date</label>
                        <input type="date" name="start_date" id="trial-start-date" min="{{ now()->toDateString() }}"
                            required value="{{ old('start_date', now()->toDateString()) }}"
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition {{ $trialErrors->has('start_date') ? 'border-red-500' : 'border-gray-300' }}">
                        @if ($trialErrors->has('start_date'))
                            <p class="mt-1 text-sm text-red-600">{{ $trialErrors->first('start_date') }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex gap-4 pt-4 border-t border-gray-200">
                    <button type="submit" id="continue-trial-modal"
                        class="px-6 py-2.5 text-white font-semibold rounded-lg transition duration-200 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="background-color: #034c8f;">
                        Start Free Trial
                    </button>
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
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

        function updateDomainPreview() {
            const domainInput = document.getElementById('trial-tenant-domain');
            const preview = document.getElementById('trial-domain-preview');

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

        document.getElementById('trial-product-search')?.addEventListener('input', filterTrialProductOptions);
        document.getElementById('trial-tenant-domain')?.addEventListener('input', updateDomainPreview);

        document.querySelectorAll('input[name="tenant_mode"]').forEach(radio => {
            radio.addEventListener('change', syncTrialTenantMode);
        });

        syncTrialTenantMode();
        updateDomainPreview();
    </script>
@endsection
