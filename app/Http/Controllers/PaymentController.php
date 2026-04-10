<?php

namespace App\Http\Controllers;

use App\Events\PaymentCompleted;
use App\Enums\PaymentMethod;
use App\Enums\PaymentPurpose;
use App\Enums\PaymentStatus;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $callbackToken = $request->header('x-callback-token');

        if ($callbackToken !== config('services.xendit.webhook_token')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payload = $request->all();

        Log::info('Xendit callback payload received.', $payload);


        if (! isset($payload['id'], $payload['status'], $payload['external_id'])) {
            Log::error('Invalid Xendit callback payload.', $payload);
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $subscription = Subscription::where('code', $payload['external_id'])->first();

        if (! $subscription) {
            Log::error('Subscription not found for Xendit external_id.', [
                'external_id' => $payload['external_id'],
            ]);
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        if ($payload['status'] === 'PAID') {
            $shouldDispatchProvisioning = false;
            $subscriptionId = $subscription->getKey();

            DB::transaction(function () use ($payload, $subscriptionId, &$shouldDispatchProvisioning) {
                $lockedSubscription = Subscription::query()
                    ->with('tenant')
                    ->lockForUpdate()
                    ->findOrFail($subscriptionId);

                $currentStatus = $lockedSubscription->payment_status?->value ?? $lockedSubscription->payment_status;

                Payment::query()->firstOrCreate(
                    [
                        'transaction_id' => $payload['id'],
                    ],
                    [
                        'subscription_id' => $lockedSubscription->getKey(),
                        'payment_purpose' => PaymentPurpose::SubscriptionPayment->value,
                        'amount' => (float) ($payload['amount'] ?? $lockedSubscription->total),
                        'payment_method' => PaymentMethod::Xendit->value,
                        'payment_status' => PaymentStatus::Completed->value,
                    ]
                );

                if ($currentStatus !== PaymentStatus::Completed->value) {
                    $lockedSubscription->forceFill([
                        'payment_status' => PaymentStatus::Completed->value,
                    ])->save();

                    $shouldDispatchProvisioning = true;
                }
            });

            if ($shouldDispatchProvisioning) {
                event(new PaymentCompleted(
                    Subscription::with('tenant')->findOrFail($subscriptionId)
                ));
            }

            Log::info('Subscription payment marked as completed.', [
                'subscription_id' => $subscriptionId,
                'external_id' => $payload['external_id'],
                'transaction_id' => $payload['id'],
                'provisioning_dispatched' => $shouldDispatchProvisioning,
            ]);
        } else {
            Log::warning('Unhandled Xendit payment status.', [
                'external_id' => $payload['external_id'],
                'status' => $payload['status'],
            ]);
        }

        return response()->json(['message' => 'Callback processed'], 200);
    }
}
