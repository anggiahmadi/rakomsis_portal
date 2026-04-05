<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted');

        $query = Payment::withCount(['subscription', 'withdrawal']);

        if (!Auth::user()->is_employee) {
            if (Auth::user()->isAgent()) {
                $query->where('agent_id', Auth::id());
            } else {
                $query->whereHas('subscription', function ($q) {
                    $q->whereHas('tenant', function ($q2) {
                        $q2->whereHas('customers', function ($q3) {
                            $q3->where('user_id', Auth::id());
                        });
                    });
                });
            }
        }

        $query = $showDeleted ? $query->onlyTrashed() : $query->whereNull('deleted_at');

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('pages.payment', compact('payments', 'showDeleted'));
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
    public function store(StorePaymentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
