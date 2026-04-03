<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->query('start_date', now()->subMonths(6)->format('Y-m-01'));
        $end = $request->query('end_date', now()->format('Y-m-t'));

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
            ->whereBetween('start_date', [$start, $end])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'totalTenants',
            'totalAgents',
            'totalWithdrawalRequests',
            'subscriptionByMonth',
            'start',
            'end'
        ));
    }
}
