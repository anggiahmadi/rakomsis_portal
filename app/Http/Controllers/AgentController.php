<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\Subscription;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $agent = Agent::where('user_id', $user->id)->first();

        if (!$agent) {
            return Redirect::route('dashboard')->with('error', 'You are not registered as an agent.');
        }

        // Get commission data for last 12 months
        $commissionData = $this->getMonthlyCommissionData($agent->id);

        // Get withdrawal statistics
        $totalWithdrawn = Withdrawal::where('agent_id', $agent->id)
            ->where('withdrawal_status', 'approved')
            ->sum('amount');

        $pendingWithdrawals = Withdrawal::where('agent_id', $agent->id)
            ->where('withdrawal_status', 'pending')
            ->sum('amount');

        $rejectedWithdrawals = Withdrawal::where('agent_id', $agent->id)
            ->where('withdrawal_status', 'rejected')
            ->count();

        // Get recent withdrawals
        $recentWithdrawals = Withdrawal::where('agent_id', $agent->id)
            ->orderBy('processed_at', 'desc')
            ->orderBy('requested_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate performance metrics
        $topCustomersCount = Subscription::where('agent_id', $agent->id)
            ->where('payment_status', PaymentStatus::Completed)
            ->count();

        $conversionRate = $topCustomersCount > 0 ? round(($topCustomersCount / max(1, $agent->total_sales)) * 100, 2) : 0;

        // Get agent level benefits
        $levelBenefits = $agent->level->benefitsArray();

        return view('dashboard.agent', [
            'user' => $user,
            'agent' => $agent,
            'commissionData' => $commissionData,
            'totalWithdrawn' => $totalWithdrawn,
            'pendingWithdrawals' => $pendingWithdrawals,
            'rejectedWithdrawals' => $rejectedWithdrawals,
            'recentWithdrawals' => $recentWithdrawals,
            'topCustomersCount' => $topCustomersCount,
            'conversionRate' => $conversionRate,
            'levelBenefits' => $levelBenefits,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgentRequest $request)
    {
        // Check if user already has an agent record
        $existingAgent = Agent::where('user_id', Auth::id())->first();
        if ($existingAgent) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already registered as an agent.'
                ], 400);
            }
            return Redirect::route('dashboard')->with('error', 'You are already registered as an agent.');
        }

        // Generate unique agent code
        $agentCode = 'AGT-' . Auth::id() . '-' . time();

        // Create agent record
        $agent = Agent::create([
            'user_id' => Auth::id(),
            'code' => $agentCode,
            'level' => 'bronze', // Default to bronze level
            'commission_rate' => 0.02, // 2% for bronze
            'discount_rate' => 0.05, // 5% for bronze
            'bank_name' => $request->bank_name ?? null,
            'bank_account_number' => $request->bank_account_number ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Agent registration successful!',
                'agent' => $agent
            ], 201);
        }

        return Redirect::route('dashboard')->with('success', 'Congratulations! You are now a Bronze agent.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agent $agent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agent $agent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgentRequest $request, Agent $agent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent)
    {
        //
    }


    /**
     * Get monthly commission data for chart
     */
    private function getMonthlyCommissionData($agentId)
    {
        $months = [];
        $commissions = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $commission = Subscription::where('agent_id', $agentId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('payment_status', PaymentStatus::Completed)
                ->sum(DB::raw('subtotal * ' . 0.02)); // Default 2% commission

            $commissions[] = round($commission, 2);
        }

        return [
            'months' => $months,
            'commissions' => $commissions,
        ];
    }

    /**
     * Store withdrawal request
     */
    public function requestWithdrawal(Request $request)
    {
        $user = Auth::user();

        $agent = Agent::where('user_id', $user->id)->first();

        if (!$agent) {
            return Redirect::route('dashboard')->with('error', 'You are not registered as an agent.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100000', 'max:' . $agent->balance],
        ]);

        // Create withdrawal request
        $withdrawal = Withdrawal::create([
            'agent_id' => $agent->id,
            'amount' => $validated['amount'],
            'withdrawal_status' => 'pending',
            'requested_at' => now(),
        ]);

        // Update agent pending withdrawal amount
        $agent->pending_withdrawal += $validated['amount'];

        $agent->balance -= $validated['amount'];

        $agent->save();

        return Redirect::route('dashboard.agent')->with('success', 'Withdrawal with total [IDR ' . number_format($withdrawal->amount, 0, ',', '.') . '] request submitted successfully!');
    }
}
