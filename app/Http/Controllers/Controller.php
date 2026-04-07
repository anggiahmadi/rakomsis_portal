<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Http;

abstract class Controller
{
    public function processingXenditInvoice($subscription): bool
    {
        $xenditSecretKey = config('services.xendit.secret_key');
        $xenditSuccessRedirectUrl = config('services.xendit.success_redirect_url');
        $xenditFailureRedirectUrl = config('services.xendit.failure_redirect_url');
        $xenditUrl = 'https://api.xendit.co/v2/invoices';

        $items = array();

        foreach ($subscription->products as $detail) {
            $detailPrice = ($subscription->price_type == 'per_user') ? $detail->price_per_user : $detail->price_per_location;

            $detailPrice = ($detailPrice * $subscription->tax_percentage / 100) + $detailPrice;

            $newItem = [
                'name' => $detail->name,
                'quantity' => $subscription->quantity * $subscription->length_of_term,
                'price' => $detailPrice,
                'category' => $detail->product_type
            ];

            array_push($items, $newItem);
        }

        $bodyJson = [
            'external_id' => $subscription->code,
            'amount' => $subscription->total,
            'description' => 'Invoice for ' . $subscription->code,
            'invoice_duration' => 86400,
            'customer' => [
                'given_name' => $subscription->customer_name,
                'surname' => $subscription->customer_name,
                'email' => $subscription->customer_email,
                'mobile_number' => $subscription->customer_phone,
            ],
            'success_redirect_url' => $xenditSuccessRedirectUrl,
            'failure_redirect_url' => $xenditFailureRedirectUrl,
            'currency' => $subscription->currency_code,
            'items' => $items
        ];

        $request = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($xenditSecretKey . ':')
        ])
            ->timeout(1000)
            ->post($xenditUrl, $bodyJson);

        if ($request->successful()) {
            // write code here to update subscription with xendit invoice id and redirect to xendit invoice page
            $response = $request->getBody()->getContents();

            $response = json_decode($response);

            $subscription->xendit_invoice_url = $response->invoice_url;

            $subscription->payment_status = PaymentStatus::Pending->value;

            $subscription->save();

            return true;
        } else {
            dd($request->body());
            // write code here to handle failed invoice creation and redirect back to subscription page with error message
            return false;
        }
    }
}
