<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->query('start_date', now()->subMonths(6)->startOfMonth()->format('Y-m-d'));
        $end = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $startDate = Carbon::parse($start)->startOfMonth();
        $endDate = Carbon::parse($end)->endOfMonth();

        $totalTenants = Tenant::count();
        $totalAgents = Agent::count();
        $totalWithdrawalRequests = Withdrawal::count();

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $monthFormat = "strftime('%Y-%m', start_date) as month";
        } elseif ($driver === 'pgsql') {
            $monthFormat = "to_char(start_date, 'YYYY-MM') as month";
        } else {
            // MySQL and MariaDB
            $monthFormat = "DATE_FORMAT(start_date, '%Y-%m') as month";
        }

        $subscriptionByMonth = Subscription::selectRaw("{$monthFormat}, SUM(total) as total")
            ->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRevenue = [];
        foreach ($subscriptionByMonth as $row) {
            $monthlyRevenue[$row->month] = (float) $row->total;
        }

        $period = CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate->copy()->startOfMonth());

        $chartLabels = [];
        $chartValues = [];
        foreach ($period as $month) {
            $key = $month->format('Y-m');
            $chartLabels[] = $month->format('M Y');
            $chartValues[] = $monthlyRevenue[$key] ?? 0;
        }

        $totalRevenue = array_sum($chartValues);

        $activeSubscriptions = Subscription::with(['tenant', 'products'])
            ->where('subscription_status', 'active')
            ->orderByDesc('start_date')
            ->limit(10)
            ->get();

        $withdrawalRequests = Withdrawal::with(['agent.user'])
            ->where('withdrawal_status', 'pending')
            ->orderByDesc('requested_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalTenants',
            'totalAgents',
            'totalWithdrawalRequests',
            'totalRevenue',
            'activeSubscriptions',
            'withdrawalRequests',
            'subscriptionByMonth',
            'chartLabels',
            'chartValues',
            'start',
            'end'
        ));
    }
}
