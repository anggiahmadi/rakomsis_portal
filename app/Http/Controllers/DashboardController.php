<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Subscription;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get customer data
        $customer = Customer::where('user_id', $user->id)->first();

        // Get tenants count
        $tenants_count = 0;
        if ($customer) {
            $tenants_count = $customer->tenants()->count();
        }

        // Get unpaid subscriptions count
        $unpaid_subscriptions = 0;
        if ($customer) {
            $tenant_ids = $customer->tenants()->pluck('id');
            $unpaid_subscriptions = Subscription::whereIn('tenant_id', $tenant_ids)
                ->where('payment_status', PaymentStatus::Pending)
                ->count();
        }

        // Get agent data
        $agent = Agent::where('user_id', $user->id)->first();

        return view('dashboard.index', [
            'user' => $user,
            'tenants_count' => $tenants_count,
            'unpaid_subscriptions' => $unpaid_subscriptions,
            'agent' => $agent,
        ]);
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();

        return view('dashboard.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return Redirect::route('dashboard.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        return view('dashboard.settings');
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return Redirect::route('dashboard.settings')->with('success', 'Password updated successfully!');
    }
}
