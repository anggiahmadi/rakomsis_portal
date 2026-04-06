@extends('components.layout.main')

@section('title', 'Admin Dashboard')

@section('content')
    @php
        $maxRevenue = !empty($chartValues) ? max($chartValues) : 0;
        $chartCount = count($chartLabels);
    @endphp

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <form method="GET" action="{{ route('admin') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $start }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $end }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-700 text-sm font-medium">
                        Apply
                    </button>
                    <a href="{{ route('admin') }}"
                        class="px-4 py-2 rounded-md border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Total Tenants</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalTenants) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Total Agents</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalAgents) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Withdrawal Requests</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalWithdrawalRequests) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Revenue (Period)</p>
                <p class="text-2xl font-bold text-emerald-700 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Monthly Revenue from Subscriptions</h3>
                <span class="text-xs text-gray-500">{{ $chartCount }} month(s)</span>
            </div>

            @if ($chartCount > 0)
                <div class="overflow-x-auto pb-2">
                    <div class="min-w-[760px] h-64 flex items-end gap-3 border-b border-l border-gray-200 px-3 pt-4">
                        @foreach ($chartValues as $index => $value)
                            @php
                                $height = $maxRevenue > 0 ? max(8, ($value / $maxRevenue) * 210) : 8;
                            @endphp
                            <div class="flex-1 min-w-[44px] flex flex-col items-center justify-end gap-2">
                                <div class="text-[11px] text-gray-500 whitespace-nowrap">
                                    {{ number_format($value, 0, ',', '.') }}
                                </div>
                                <div class="w-full bg-blue-500 hover:bg-blue-600 rounded-t transition-colors"
                                    title="{{ $chartLabels[$index] }}" style="height: {{ $height }}px"></div>
                                <div class="text-[11px] text-gray-600 text-center whitespace-nowrap">
                                    {{ $chartLabels[$index] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500">No revenue data available for selected period.</p>
            @endif
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Active Subscriptions</h3>
                </div>
                @if ($activeSubscriptions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Tenant</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Start</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($activeSubscriptions as $subscription)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-900">{{ $subscription->code }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $subscription->tenant?->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ optional($subscription->start_date)->format('M d, Y') ?? '-' }}</td>
                                        <td class="px-4 py-3 text-emerald-700 font-semibold">
                                            Rp {{ number_format((float) $subscription->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="px-6 py-6 text-sm text-gray-500">No active subscriptions found.</p>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Request Withdrawals (Pending)</h3>
                </div>
                @if ($withdrawalRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Agent</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Requested At</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($withdrawalRequests as $withdrawal)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-900">{{ $withdrawal->agent?->user?->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-amber-700 font-semibold">
                                            Rp {{ number_format((float) $withdrawal->amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ optional($withdrawal->requested_at)->format('M d, Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">
                                                {{ ucfirst((string) $withdrawal->withdrawal_status->value) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="px-6 py-6 text-sm text-gray-500">No pending withdrawal requests.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
