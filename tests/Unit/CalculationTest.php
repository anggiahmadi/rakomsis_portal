<?php

use App\Enums\PaymentStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Agent;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Withdrawal;

test('calculate agent commission', function () {
    // Create a new customer using the factory
    $customer = Customer::factory()->create();
    // Create a tenant for customer
    $tenant = $customer->tenants()->create([
        'code' => 'TENANT-' . strtoupper(uniqid()),
        'name' => $customer->user->name . "'s Tenant",
        'domain' => strtolower(uniqid()) . '.example.com',
    ]);
    // Register to be an agent
    $agent = Agent::factory()->create([
        'user_id' => $customer->user->id,
        'level' => 'bronze',
        'commission_rate' => 0.02,
        'discount_rate' => 0.05,
        'total_sales' => 0, // Total sales to determine agent level
        'total_commission' => 0, // Total commission earned by the agent
        'balance' => 0, // Balance available for withdrawal
        'withdrawn' => 0, // Total amount withdrawn by the agent
        'pending_withdrawal' => 0 // Amount pending withdrawal
    ]);

    // Create a product
    $product = Product::factory()->create();

    // Subscribe the tenant to the promotion
    $sucbscription = Subscription::factory()->create([
        'tenant_id' => $tenant->id,
        'agent_id' => $agent->id,
        'product_id' => $product->id,
        'tax_percentage' => 11.00,
        'price' => 100.00,
        'tax' => 11.00,
        'discount_type' => 'percentage',
        'discount' => 100.00 * $agent->discount_rate, // 10% discount for silver level
        'subtotal' => 100.00 - (100.00 * $agent->discount_rate), // price before tax and discount
        'total' => (100.00 - (100.00 * $agent->discount_rate)) + 11.00, // total price after discount and tax
        'agent_commission' => (100.00 - (100.00 * $agent->commission_rate)) * $agent->commission_rate, // commission based on subtotal
        'payment_status' => 'pending',
    ]);

    $payment = $sucbscription->payments()->create([
        'payment_purpose' => 'subscription_payment',
        'amount' => $sucbscription->total,
        'payment_method' => 'credit_card',
        'payment_status' => 'completed',
        'transaction_id' => uniqid(),
    ]);

    $sucbscription->update([
        'payment_status' => PaymentStatus::Completed,
    ]);

    expect($payment->amount)->toBe($sucbscription->total);

    expect($payment->payment_status)->toBe(PaymentStatus::Completed);

    expect($sucbscription->payment_status)->toBe(PaymentStatus::Completed);

    expect($sucbscription->agent_commission)->toBe((100.00 - (100.00 * $agent->commission_rate)) * $agent->commission_rate);

    $agent->update([
        'total_sales' => $agent->total_sales + $sucbscription->subtotal,
        'total_commission' => $agent->total_commission + $sucbscription->agent_commission,
        'balance' => $agent->balance + $sucbscription->agent_commission,
    ]);

    expect($agent->total_sales)->toBe($sucbscription->subtotal);

    expect($agent->total_commission)->toBe($sucbscription->agent_commission);

    expect($agent->balance)->toBe($sucbscription->agent_commission);
});

test('calculate agent commission with failed payment', function () {
    // Create a new customer using the factory
    $customer = Customer::factory()->create();
    // Create a tenant for customer
    $tenant = $customer->tenants()->create([
        'code' => 'TENANT-' . strtoupper(uniqid()),
        'name' => $customer->user->name . "'s Tenant",
        'domain' => strtolower(uniqid()) . '.example.com',
    ]);
    // Register to be an agent
    $agent = Agent::factory()->create([
        'user_id' => $customer->user->id,
        'level' => 'bronze',
        'commission_rate' => 0.02,
        'discount_rate' => 0.05,
        'total_sales' => 0, // Total sales to determine agent level
        'total_commission' => 0, // Total commission earned by the agent
        'balance' => 0, // Balance available for withdrawal
        'withdrawn' => 0, // Total amount withdrawn by the agent
        'pending_withdrawal' => 0 // Amount pending withdrawal
    ]);

    // Create a product
    $product = Product::factory()->create();

    // Subscribe the tenant to the promotion
    $sucbscription = Subscription::factory()->create([
        'tenant_id' => $tenant->id,
        'agent_id' => $agent->id,
        'product_id' => $product->id,
        'tax_percentage' => 11.00,
        'price' => 100.00,
        'tax' => 11.00,
        'discount_type' => 'percentage',
        'discount' => 100.00 * $agent->discount_rate, // 10% discount for silver level
        'subtotal' => 100.00 - (100.00 * $agent->discount_rate), // price before tax and discount
        'total' => (100.00 - (100.00 * $agent->discount_rate)) + 11.00, // total price after discount and tax
        'agent_commission' => (100.00 - (100.00 * $agent->commission_rate)) * $agent->commission_rate, // commission based on subtotal
        'payment_status' => 'pending',
    ]);

    $payment = $sucbscription->payments()->create([
        'payment_purpose' => 'subscription_payment',
        'amount' => $sucbscription->total,
        'payment_method' => 'credit_card',
        'payment_status' => 'failed',
        'transaction_id' => uniqid(),
    ]);

    $sucbscription->update([
        'payment_status' => PaymentStatus::Failed,
    ]);

    expect($payment->amount)->toBe($sucbscription->total);

    expect($payment->payment_status)->toBe(PaymentStatus::Failed);

    expect($sucbscription->payment_status)->toBe(PaymentStatus::Failed);

    expect($agent->total_sales)->toBe(0.0);

    expect($agent->total_commission)->toBe(0.0);

    expect($agent->balance)->toBe(0.0);
});

test('calculate agent withdrawal', function () {
    // Create a new customer using the factory
    $customer = Customer::factory()->create();
    // Register to be an agent
    $agent = Agent::factory()->create([
        'user_id' => $customer->user->id,
        'level' => 'bronze',
        'commission_rate' => 0.02,
        'discount_rate' => 0.05,
        'total_sales' => 0, // Total sales to determine agent level
        'total_commission' => 0, // Total commission earned by the agent
        'balance' => 100.00, // Balance available for withdrawal
        'withdrawn' => 0, // Total amount withdrawn by the agent
        'pending_withdrawal' => 0 // Amount pending withdrawal
    ]);

    $withdrawal = $agent->withdrawals()->create([
        'amount' => 50.00,
        'withdrawal_status' => WithdrawalStatus::Pending,
    ]);

    expect($withdrawal->amount)->toBe(50.00);

    expect($withdrawal->withdrawal_status)->toBe(WithdrawalStatus::Pending);

    $withdrawal->payments()->create([
        'payment_purpose' => 'commission_payout',
        'amount' => $withdrawal->amount,
        'payment_method' => 'bank_transfer',
        'payment_status' => 'completed',
        'transaction_id' => uniqid(),
    ]);

    $withdrawal->update([
        'withdrawal_status' => WithdrawalStatus::Approved,
    ]);

    $agent->update([
        'balance' => $agent->balance - $withdrawal->amount,
        'withdrawn' => $agent->withdrawn + $withdrawal->amount,
    ]);

    expect($withdrawal->withdrawal_status)->toBe(WithdrawalStatus::Approved);

    expect($agent->balance)->toBe(50.00);

    expect($agent->withdrawn)->toBe(50.00);
});
