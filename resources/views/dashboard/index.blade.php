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
                        <p class="text-2xl font-bold text-gray-900 mt-1 leading-tight">
                            @if ($tenants_count > 0)
                                {{ $tenants_count }}
                            @else
                                <a href="{{ route('dashboard.free-trial') }}"
                                    class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg text-white font-semibold"
                                    style="background-color: #034c8f;">
                                    Start Free Trial
                                </a>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-2">Manage your tenants and launch your first workspace.</p>
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
                        <p class="text-lg font-bold text-gray-900 mt-1 leading-tight">
                            @if ($total_subscription > 0)
                                <a href="{{ route('subscriptions.index') }}"
                                    class="hover:text-[#00a8e3]">{{ $total_subscription }} Active
                                    Subscription(s)</a>
                            @else
                                <a href="{{ route('dashboard.free-trial') }}"
                                    class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg text-white font-semibold"
                                    style="background-color: #00a8e3;">
                                    Start Free Trial
                                </a>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-2">Track active plans and billing cycle status.</p>
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
                                    Become an Agent
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
                                    Become an Agent
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

    <!-- Agent Info Modal -->
    <div id="agent-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8 hidden">
        <div class="bg-white rounded-xl w-full max-w-md shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Become an Agent</h3>
                <button id="close-agent-modal" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>

            <div class="p-6 text-sm text-gray-700 space-y-4">
                <p>Join our agent program and unlock extra benefits for your referrals.</p>
                <ul class="list-disc pl-5 space-y-1 text-gray-600">
                    <li>Start at <strong>Bronze level</strong></li>
                    <li>Earn commission from referred subscriptions</li>
                    <li>Get customer discount support</li>
                </ul>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
                <button id="cancel-agent-modal"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                    Cancel
                </button>
                <button id="continue-agent-modal" class="px-4 py-2 rounded-lg text-white font-semibold"
                    style="background-color: #034c8f;">
                    Continue
                </button>
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
