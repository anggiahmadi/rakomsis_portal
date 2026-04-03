<nav class="p-4 space-y-2">
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
