<nav class="p-4 space-y-2">
    @if (Auth::user() && !Auth::user()->isAdmin())
        <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Dashboard</div>
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-[#034c8f] font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
            style="{{ request()->routeIs('dashboard') ? 'border-left: 4px solid #034c8f;' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                </path>
            </svg>
            Dashboard
        </a>
    @else
        <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Admin</div>

        @php
            $adminItems = [
                [
                    'name' => 'Admin',
                    'route' => 'admin',
                    'icon' =>
                        'M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z',
                ],
                [
                    'name' => 'Product',
                    'route' => 'products.index',
                    'icon' =>
                        'M21 16.5c0 .38-.21.71-.53.88l-7.97 4.44c-.31.17-.69.17-1 0L3.53 17.38c-.32-.17-.53-.5-.53-.88V7.5c0-.38.21-.71.53-.88l7.97-4.44c.31-.17.69-.17 1 0l7.97 4.44c.32.17.53.5.53.88v9zM12 4.15L5.04 8.02 12 11.85l6.96-3.83L12 4.15z',
                ],
                [
                    'name' => 'Promotion',
                    'route' => 'promotions.index',
                    'icon' => 'M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z',
                ],
                [
                    'name' => 'Customer',
                    'route' => 'customers.index',
                    'icon' =>
                        'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z',
                ],
            ];
        @endphp

        @foreach ($adminItems as $item)
            <a href="{{ route($item['route']) }}"
                class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs($item['route']) ? 'bg-blue-50 text-[#034c8f] font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                style="{{ request()->routeIs($item['route']) ? 'border-left: 4px solid #034c8f;' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="{{ $item['icon'] }}"></path>
                </svg>
                {{ $item['name'] }}
            </a>
        @endforeach
    @endif

    @php
        $agent = Auth::user()->agent ?? null;
    @endphp

    @if ($agent)
        <a href="{{ route('dashboard.agent') }}"
            class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('dashboard.agent') ? 'bg-blue-50 text-[#034c8f] font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
            style="{{ request()->routeIs('dashboard.agent') ? 'border-left: 4px solid #034c8f;' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                    clip-rule="evenodd"></path>
            </svg>
            Agent
        </a>
    @endif

    <hr class="my-4">
    <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Main Transaction</div>

    @php
        $pages = [
            [
                'name' => 'Tenant',
                'route' => 'tenants.index',
                'icon' => 'M12 3L2 12h3v8h14v-8h3L12 3zm0 11a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm-4 4a4 4 0 0 1 8 0H8z',
            ],
            [
                'name' => 'Subscription',
                'route' => 'subscriptions.index',
                'icon' =>
                    'M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm-9-2l-4-4 1.41-1.41L10 15.17l6.59-6.59L18 10l-8 8z',
            ],
            [
                'name' => 'Payment',
                'route' => 'payments.index',
                'icon' =>
                    'M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z',
            ],
        ];

        if (Auth::user()->isAdmin()) {
            $pages[] = [
                'name' => 'Withdrawal',
                'route' => 'withdrawals.index',
                'icon' =>
                    'M19 15v4H5v-4H3v4c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-4h-2zm-6-2.83l1.59-1.58L16 12l-4 4-4-4 1.41-1.41L11 12.17V3h2v9.17z',
            ];
        } else {
            if (Auth::user()->agent) {
                $pages[] = [
                    'name' => 'Withdrawal',
                    'route' => 'withdrawals.index',
                    'icon' =>
                        'M19 15v4H5v-4H3v4c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-4h-2zm-6-2.83l1.59-1.58L16 12l-4 4-4-4 1.41-1.41L11 12.17V3h2v9.17z',
                ];
            }
        }
    @endphp

    @foreach ($pages as $page)
        <a href="{{ route($page['route']) }}"
            class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs($page['route']) ? 'bg-blue-50 text-[#034c8f] font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
            style="{{ request()->routeIs($page['route']) ? 'border-left: 4px solid #034c8f;' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="{{ $page['icon'] }}"></path>
            </svg>
            {{ $page['name'] }}
        </a>
    @endforeach

    <hr class="my-4">
    <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Account</div>

    <a href="{{ route('dashboard.profile') }}"
        class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('dashboard.profile') ? 'bg-blue-50 text-[#034c8f] font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
        style="{{ request()->routeIs('dashboard.profile') ? 'border-left: 4px solid #034c8f;' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">
            </path>
        </svg>
        Profile
    </a>

    <a href="{{ route('dashboard.settings') }}"
        class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('dashboard.settings') ? 'bg-blue-50 text-[#034c8f] font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
        style="{{ request()->routeIs('dashboard.settings') ? 'border-left: 4px solid #034c8f;' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.533 1.533 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.533 1.533 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                clip-rule="evenodd"></path>
        </svg>
        Settings
    </a>

    <form method="POST" action="{{ url('logout') }}" class="w-full">
        @csrf
        <button type="submit"
            class="w-full flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9l-2.293 2.293z"
                    clip-rule="evenodd"></path>
            </svg>
            Logout
        </button>
    </form>
</nav>
