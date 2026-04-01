<?php

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Withdrawal;

test('could register a customer', function () {
    // Create a new customer using the factory
    $customer = Customer::factory()->create();

    // Assert that the customer was created successfully
    expect($customer)->toBeInstanceOf(Customer::class);

    // Assert that the customer has an associated user account $customer->load('user'); // Load the user relationship
    expect($customer->user)->toBeInstanceOf(User::class);

    // Assert that the user have null agent_id expect($customer->user->agent_id)->toBeNull();
    expect($customer->user->agent)->toBeNull();
});

test('could register a tenant for a customer', function () {
    // Create a new customer using the factory
    $customer = Customer::factory()->create();
    // Create a tenant for customer
    $tenant = $customer->tenants()->create([
        'code' => 'TENANT-' . strtoupper(uniqid()),
        'name' => $customer->user->name . "'s Tenant",
        'domain' => strtolower(uniqid()) . '.example.com',
    ]);
    // Assert that the tenant was created successfully
    expect($tenant)->toBeInstanceOf(Tenant::class);
});

test('could register an agent for a customer', function () {
    // Create a new customer using the factory
    $customer = Customer::factory()->create();
    // Register to be an agent
    $agent = Agent::factory()->create([
        'user_id' => $customer->user->id,
    ]);

    // Assert that the agent was created successfully
    expect($agent)->toBeInstanceOf(Agent::class);

    // Assert that the agent has an associated user account $agent->load('user'); // Load the user relationship
    expect($agent->user)->toBeInstanceOf(User::class);

});

test('could register an employee', function () {
    // Create an employee user
    $employee = Employee::factory()->create();

    // Check that user is employee
    expect($employee->user->is_employee)->toBeTrue();

});

test('could register a product', function () {
    // Create a product
    $product = Product::factory()->create();

    // Assert that the product was created successfully
    expect($product)->toBeInstanceOf(Product::class);
});

test('could register a promotion', function () {
    // Create a promotion
    $promotion = Promotion::factory()->create();

    // Assert that the promotion was created successfully
    expect($promotion)->toBeInstanceOf(Promotion::class);
});

test('could subscribe a tenant to get a promotion', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create a product
    $product = Product::factory()->create();

    // Create a promotion
    $promotion = Promotion::factory()->create();

    // Subscribe the tenant to the promotion
    $sucbscription = Subscription::factory()->create([
        'tenant_id' => $tenant->id,
        'product_id' => $product->id,
        'promotion_id' => $promotion->id,
    ]);

    // Assert that the subscription was created successfully
    expect($sucbscription)->toBeInstanceOf(Subscription::class);

    // Assert that the subscription has an associated tenant // Load the user relationship
    expect($sucbscription->tenant)->toBeInstanceOf(Tenant::class);

    // Assert that the subscription has an associated product // Load the user relationship
    expect($sucbscription->product)->toBeInstanceOf(Product::class);

    // Assert that the subscription has an associated promotion // Load the user relationship
    expect($sucbscription->promotion)->toBeInstanceOf(Promotion::class);

    $payment = $sucbscription->payments()->create([
        'payment_purpose' => 'subscription_payment',
        'amount' => $sucbscription->total,
        'payment_method' => 'credit_card',
        'payment_status' => 'pending',
        'transaction_id' => uniqid(),
    ]);

    // Assert that the payment was created successfully
    expect($payment)->toBeInstanceOf(Payment::class);

    // Assert that the payment has an associated subscription // Load the subscription relationship
    $payment->load('subscription'); // Load the subscription relationship
    expect($payment->subscription)->toBeInstanceOf(Subscription::class);
});

test('could register a withdrawal for an agent', function () {
    // Create a new customer using the factory
    $customer = Customer::factory()->create();
    // Register to be an agent
    $agent = Agent::factory()->create([
        'user_id' => $customer->user->id,
    ]);

    // Create a withdrawal for the agent
    $withdrawal = $agent->withdrawals()->create([
        'amount' => 100.00,
        'withdrawal_status' => 'pending',
    ]);

    // Assert that the withdrawal was created successfully
    expect($withdrawal)->toBeInstanceOf(Withdrawal::class);

    // Assert that the withdrawal has an associated agent // Load the agent relationship
    expect($withdrawal->agent)->toBeInstanceOf(Agent::class);

    $payment = $withdrawal->payments()->create([
        'payment_purpose' => 'commission_payout',
        'amount' => $withdrawal->amount,
        'payment_method' => 'bank_transfer',
        'payment_status' => 'pending',
        'transaction_id' => uniqid(),
    ]);

    // Assert that the payment was created successfully
    expect($payment)->toBeInstanceOf(Payment::class);

    // Assert that the payment has an associated withdrawal // Load the withdrawal relationship
    $payment->load('withdrawal'); // Load the withdrawal relationship
    expect($payment->withdrawal)->toBeInstanceOf(Withdrawal::class);
});
