<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\Subscription;
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

    public function generateXenditInvoice(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);

        if (! in_array($subscription->payment_status?->value ?? $subscription->payment_status, [PaymentStatus::NotPaid->value, PaymentStatus::Failed->value], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice can only be generated for Not Paid or Failed subscriptions.',
            ], 422);
        }

        if (! $this->processingXenditInvoice($subscription)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Xendit invoice.',
            ], 500);
        }

        $subscription->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Xendit invoice generated successfully.',
            'invoice_url' => $subscription->xendit_invoice_url,
        ]);
    }

    public function xenditCallback(Request $request)
    {
        $payload = $request->all();

        // Log the payload for debugging
        \Log::info('Xendit Callback Payload:', $payload);


        // Validate the payload
        if (!isset($payload['id']) || !isset($payload['status']) || !isset($payload['external_id'])) {
            \Log::error('Invalid Xendit callback payload', $payload);
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Find the payment by external_id

        $subscription = Subscription::where('external_id', $payload['external_id'])->first();

        if (!$subscription) {
            \Log::error('Subscription not found for external_id: ' . $payload['external_id']);
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        // Update payment status based on Xendit callback
        if ($payload['status'] === 'PAID') {
            $subscription->payments()->create([
                'amount' => $payload['amount'],
                'payment_method' => $payload['payment_method'] ?? 'unknown',
                'status' => PaymentStatus::Completed->value,
                'transaction_id' => $payload['id'],
            ]);

            $subscription->update([
                'payment_status' => PaymentStatus::Completed->value,
            ]);

            \Log::info('Payment marked as paid for external_id: ' . $payload['external_id']);
        } else {
            \Log::warning('Unhandled payment status from Xendit: ' . $payload['status']);
        }

        return response()->json(['message' => 'Callback processed'], 200);
    }
}
