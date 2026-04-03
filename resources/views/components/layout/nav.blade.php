<nav class="p-4 space-y-2">
    @if (Auth::user() && !Auth::user()->isAdmin())
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
    @else
        <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Admin</div>

        @php
            $adminItems = [
                [
                    'name' => 'Admin Dashboard',
                    'route' => 'admin',
                    'icon' => 'M10 2a8 8 0 100 16 8 8 0 000-16z',
                ],
                [
                    'name' => 'Product',
                    'route' => 'products.index',
                    'icon' => 'M4 3h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1z',
                ],
                ['name' => 'Promotion', 'route' => 'promotions.index', 'icon' => 'M5 5h10v10H5z'],
                ['name' => 'Customer', 'route' => 'customers.index', 'icon' => 'M8 7a2 2 0 114 0 2 2 0 01-4 0z'],
                ['name' => 'Tenant', 'route' => 'tenants.index', 'icon' => 'M5 7h10v3H5z'],
                ['name' => 'Agent', 'route' => 'agents.index', 'icon' => 'M3 4h14v12H3z'],
                ['name' => 'Subscription', 'route' => 'subscriptions.index', 'icon' => 'M6 2h8v4H6z'],
                ['name' => 'Withdrawal', 'route' => 'withdrawals.index', 'icon' => 'M5 10h10v2H5z'],
                ['name' => 'Payment', 'route' => 'payments.index', 'icon' => 'M5 6h10v2H5z'],
            ];
        @endphp

        @foreach ($adminItems as $item)
            <a href="{{ route($item['route']) }}"
                class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs($item['route']) ? 'bg-blue-50 text-[#034c8f] font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                style="{{ request()->routeIs($item['route']) ? 'border-left: 4px solid #034c8f;' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
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
