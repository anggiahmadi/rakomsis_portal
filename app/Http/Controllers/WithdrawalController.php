<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWithdrawalRequest;
use App\Http\Requests\UpdateWithdrawalRequest;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted');

        $query = Withdrawal::withCount(['agent', 'payments']);

        if (!Auth::user()->is_employee) {
            if (Auth::user()->isAgent()) {
                $query->whereHas('agent', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            } else {
                return Redirect::route('dashboard')->with('error', 'Unauthorized access.');
            }
        }

        $query = $showDeleted ? $query->onlyTrashed() : $query->whereNull('deleted_at');

        $withdrawals = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('pages.withdrawal', compact('withdrawals', 'showDeleted'));
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
    public function store(StoreWithdrawalRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Withdrawal $withdrawal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Withdrawal $withdrawal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWithdrawalRequest $request, Withdrawal $withdrawal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Withdrawal $withdrawal)
    {
        //
    }
}
