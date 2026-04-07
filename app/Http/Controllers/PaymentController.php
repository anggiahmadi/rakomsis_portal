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

    public function xenditCallback(Request $request)
    {
        $payload = $request->all();

        // Log the payload for debugging
        \Log::info('Xendit Callback Payload:', $payload);

        // // Validate the payload
        // if (!isset($payload['id']) || !isset($payload['status']) || !isset($payload['external_id'])) {
        //     \Log::error('Invalid Xendit callback payload', $payload);
        //     return response()->json(['message' => 'Invalid payload'], 400);
        // }

        // // Find the payment by external_id
        // $payment = Payment::where('external_id', $payload['external_id'])->first();

        // if (!$payment) {
        //     \Log::error('Payment not found for external_id: ' . $payload['external_id']);
        //     return response()->json(['message' => 'Payment not found'], 404);
        // }

        // // Update payment status based on Xendit callback
        // if ($payload['status'] === 'PAID') {
        //     $payment->status = 'paid';
        //     $payment->save();
        //     \Log::info('Payment marked as paid for external_id: ' . $payload['external_id']);
        // } else {
        //     \Log::warning('Unhandled payment status from Xendit: ' . $payload['status']);
        // }

        return response()->json(['message' => 'Callback processed'], 200);
    }
}
