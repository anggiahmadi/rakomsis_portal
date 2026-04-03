@extends('components.layout.main')

@section('title', 'Agent Dashboard')

@section('content')
    @php
        // Define color schemes based on agent level
        $levelColors = match ($agent->level->name) {
            'Bronze' => [
                'primary' => '#CD7F32', // Copper
                'secondary' => '#A0522D', // Brown
                'accent' => '#D2691E', // Chocolate
                'light' => '#F4E4BC', // Light copper
                'gradient_from' => '#CD7F32',
                'gradient_to' => '#A0522D',
            ],
            'Silver' => [
                'primary' => '#C0C0C0', // Silver
                'secondary' => '#808080', // Gray
                'accent' => '#A9A9A9', // Dark gray
                'light' => '#F5F5F5', // Light gray
                'gradient_from' => '#C0C0C0',
                'gradient_to' => '#808080',
            ],
            'Gold' => [
                'primary' => '#FFD700', // Gold
                'secondary' => '#FFA500', // Orange
                'accent' => '#FF8C00', // Dark orange
                'light' => '#FFF8DC', // Cornsilk
                'gradient_from' => '#FFD700',
                'gradient_to' => '#FFA500',
            ],
            default => [
                'primary' => '#034c8f', // Default blue
                'secondary' => '#00a8e3', // Default cyan
                'accent' => '#7a8b9c', // Default gray
                'light' => '#f8f9fa', // Default light
                'gradient_from' => '#034c8f',
                'gradient_to' => '#00a8e3',
            ],
        };
    @endphp
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r rounded-lg shadow-lg p-6 text-white"
            style="background: linear-gradient(to right, {{ $levelColors['gradient_from'] }}, {{ $levelColors['gradient_to'] }});">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-1">Welcome, Agent {{ $agent->code }} 👨‍💼</h1>
                    <p class="opacity-90">Manage your commissions, withdrawals, and performance</p>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90">Your Level</p>
                    <p class="text-2xl font-bold">{{ $agent->level->name }}</p>
                </div>
            </div>
        </div>

        <!-- Agent Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Agent Code & ID -->
            <div class="rounded-lg shadow p-6 border-l-4 text-white"
                style="background: linear-gradient(135deg, {{ $levelColors['primary'] }}, {{ $levelColors['secondary'] }}); border-left-color: {{ $levelColors['accent'] }};">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium opacity-90">Agent ID</p>
                    <button onclick="shareAgentCode('{{ $agent->code }}')"
                        class="text-xs bg-white/20 hover:bg-white/30 px-2 py-1 rounded text-white transition">
                        📤 Share
                    </button>
                </div>
                <p class="text-2xl font-bold font-mono">{{ $agent->code }}</p>
                <p class="text-xs opacity-75 mt-3">Reference code for customers</p>
            </div>

            <!-- Commission Rate -->
            <div class="rounded-lg shadow p-6 border-l-4 text-white"
                style="background: linear-gradient(135deg, {{ $levelColors['secondary'] }}, {{ $levelColors['primary'] }}); border-left-color: {{ $levelColors['accent'] }};">
                <p class="text-sm font-medium opacity-90">Commission Rate</p>
                <p class="text-2xl font-bold mt-2">{{ number_format($agent->commission_rate * 100, 1) }}%</p>
                <p class="text-xs opacity-75 mt-3">Earned on each referral</p>
            </div>

            <!-- Discount Rate -->
            <div class="rounded-lg shadow p-6 border-l-4 text-white"
                style="background: linear-gradient(135deg, {{ $levelColors['accent'] }}, {{ $levelColors['secondary'] }}); border-left-color: {{ $levelColors['primary'] }};">
                <p class="text-sm font-medium opacity-90">Discount Rate</p>
                <p class="text-2xl font-bold mt-2">{{ number_format($agent->discount_rate * 100, 1) }}%</p>
                <p class="text-xs opacity-75 mt-3">Customer discount you provide</p>
            </div>

            <!-- Total Sales -->
            <div class="rounded-lg shadow p-6 border-l-4 text-white"
                style="background: linear-gradient(135deg, {{ $levelColors['primary'] }}, {{ $levelColors['accent'] }}); border-left-color: {{ $levelColors['secondary'] }};">
                <p class="text-sm font-medium opacity-90">Total Sales Generated</p>
                <p class="text-2xl font-bold mt-2">Rp {{ number_format($agent->total_sales, 0, ',', '.') }}</p>
                <p class="text-xs opacity-75 mt-3">{{ $topCustomersCount }} Active Customers</p>
            </div>
        </div>

        <!-- Commission & Financial Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Total Commission -->
            <div class="bg-white rounded-lg shadow p-6 border-t-4"
                style="border-top-color: {{ $levelColors['primary'] }};">
                <p class="text-gray-600 text-sm font-medium">Total Commission Earned</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">Rp
                    {{ number_format($agent->total_commission, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-2">All-time earnings</p>
            </div>

            <!-- Available Balance -->
            <div class="bg-white rounded-lg shadow p-6 border-t-4"
                style="border-top-color: {{ $levelColors['secondary'] }};">
                <p class="text-gray-600 text-sm font-medium">Available Balance</p>
                <p class="text-3xl font-bold text-green-600 mt-2">Rp {{ number_format($agent->balance, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-2">Ready to withdraw</p>
            </div>

            <!-- Pending Withdrawal -->
            <div class="bg-white rounded-lg shadow p-6 border-t-4" style="border-top-color: {{ $levelColors['accent'] }};">
                <p class="text-gray-600 text-sm font-medium">Pending Withdrawal</p>
                <p class="text-3xl font-bold text-orange-600 mt-2">Rp {{ number_format($pendingWithdrawals, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $recentWithdrawals->where('withdrawal_status', 'pending')->count() }} requests</p>
            </div>
        </div>

        <!-- Commission Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Commission Distribution (Last 12 Months)</h2>
            <div class="relative h-80">
                <canvas id="commissionChart"></canvas>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Conversion Rate -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Conversion Rate</p>
                <div class="mt-4">
                    <p class="text-3xl font-bold text-blue-600">{{ $conversionRate }}%</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($conversionRate, 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ $topCustomersCount }} referrals converted</p>
                </div>
            </div>

            <!-- Total Withdrawn -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Total Withdrawn</p>
                <p class="text-3xl font-bold text-purple-600 mt-4">Rp {{ number_format($totalWithdrawn, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-2">Approved & Processed</p>
            </div>

            <!-- Rejection Rate -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Withdrawal Status</p>
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pending</span>
                        <span
                            class="font-bold text-gray-900">{{ $recentWithdrawals->where('withdrawal_status', 'pending')->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Approved</span>
                        <span
                            class="font-bold text-green-600">{{ $recentWithdrawals->where('withdrawal_status', 'approved')->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Rejected</span>
                        <span class="font-bold text-red-600">{{ $rejectedWithdrawals }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawal Section -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Withdrawal Request Form -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow p-6 border-l-4" style="border-left-color: #034c8f;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Withdrawal</h3>

                @if ($agent->balance > 0)
                    <form action="{{ route('agent.withdrawal.request') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Withdrawal Amount</label>
                            <div class="mt-1">
                                <span class="text-sm text-gray-500">Rp</span>
                                <input type="number" name="amount" id="amount"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    placeholder="100000" min="100000" max="{{ $agent->balance }}" step="100000"
                                    required>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Min: Rp 100.000 | Max: Rp
                                {{ number_format($agent->balance, 0, ',', '.') }}</p>
                        </div>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-[#034c8f] to-[#00a8e3] hover:shadow-lg text-white font-semibold py-2 px-4 rounded-lg transition">
                            Request Withdrawal
                        </button>
                    </form>
                @else
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">No balance available for withdrawal.</p>
                        <p class="text-xs text-blue-600 mt-2">Keep growing your sales to earn commissions!</p>
                    </div>
                @endif
            </div>

            <!-- Withdrawal History -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Withdrawal History</h3>
                    </div>

                    @if ($recentWithdrawals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Date
                                            Requested</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Amount
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Processed Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($recentWithdrawals as $withdrawal)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $withdrawal->requested_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                                Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-semibold
                                                    @if ($withdrawal->withdrawal_status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif ($withdrawal->withdrawal_status === 'approved')
                                                        bg-green-100 text-green-800
                                                    @else
                                                        bg-red-100 text-red-800 @endif
                                                ">
                                                    {{ ucfirst($withdrawal->withdrawal_status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                @if ($withdrawal->processed_at)
                                                    {{ $withdrawal->processed_at->format('M d, Y H:i') }}
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-6 py-8 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4">
                                </path>
                            </svg>
                            <p class="text-gray-500">No withdrawal requests yet.</p>
                            <p class="text-sm text-gray-400 mt-1">Start by requesting a withdrawal above!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Agent Level Benefits Info -->
        <div class="rounded-lg shadow p-6 border-l-4"
            style="background: linear-gradient(135deg, {{ $levelColors['light'] }}, rgba(255,255,255,0.9)); border-left-color: {{ $levelColors['primary'] }};">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Your {{ $agent->level->name }} Level Benefits</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($levelBenefits as $key => $value)
                    <div class="rounded-lg p-4 text-white"
                        style="background: linear-gradient(135deg, {{ $levelColors['primary'] }}, {{ $levelColors['secondary'] }});">
                        <p class="text-sm font-medium opacity-90">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                        <p class="text-lg font-bold text-[#034c8f] mt-2">
                            @if (is_numeric($value))
                                {{ number_format($value, 2) }}
                            @else
                                {{ $value }}
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Level Progression Info -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Progress to Next Level</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if ($agent->level->name === 'Bronze')
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Reach Silver Level</p>
                                <p class="text-sm text-gray-600">Generate Rp 100,000,000 in sales</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Current Progress</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                    style="width: {{ min(($agent->total_sales / 100000000) * 100, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($agent->total_sales, 0, ',', '.') }}
                                / Rp 100,000,000</p>
                        </div>
                    @elseif ($agent->level->name === 'Silver')
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Reach Gold Level</p>
                                <p class="text-sm text-gray-600">Generate Rp 1,000,000,000 in sales</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Current Progress</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                    style="width: {{ min(($agent->total_sales / 1000000000) * 100, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($agent->total_sales, 0, ',', '.') }}
                                / Rp 1,000,000,000</p>
                        </div>
                    @else
                        <div class="col-span-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <p class="text-sm font-medium text-gray-900">You've reached the highest level!</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script for Commission Distribution -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('commissionChart').getContext('2d');
        const commissionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($commissionData['months']) !!},
                datasets: [{
                    label: 'Commission Earned (Rp)',
                    data: {!! json_encode($commissionData['commissions']) !!},
                    backgroundColor: '{{ $levelColors['primary'] }}99', // 60% opacity
                    borderColor: '{{ $levelColors['primary'] }}',
                    borderWidth: 2,
                    borderRadius: 5,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>

    <!-- Share Agent Code Script -->
    <script>
        function shareAgentCode(agentCode) {
            const shareText =
                `Join Rakomsis with my agent code: ${agentCode}\n\nGet exclusive discounts and earn commissions together! 🚀`;
            const shareUrl = window.location.origin + '/subscribe?ref=' + agentCode;

            // Check if Web Share API is supported
            if (navigator.share) {
                navigator.share({
                    title: 'Rakomsis Agent Referral',
                    text: shareText,
                    url: shareUrl
                }).catch(console.error);
            } else {
                // Fallback: Copy to clipboard
                navigator.clipboard.writeText(`${shareText}\n\n${shareUrl}`).then(() => {
                    // Show success message
                    showNotification('Agent code copied to clipboard!', 'success');
                }).catch(() => {
                    // Fallback: Show text in alert
                    alert(`Share this code with others:\n\n${agentCode}\n\n${shareText}`);
                });
            }
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;

            // Add to page
            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
@endsection
