<?php

use App\Models\Agent;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tenant::class)->constrained()->onDelete('cascade'); // Each subscription belongs to a tenant
            $table->foreignIdFor(Agent::class)->nullable()->constrained()->onDelete('set null'); // Optional agent associated with the subscription for commission tracking
            $table->foreignIdFor(Promotion::class)->nullable()->constrained()->onDelete('set null'); // Optional promotion applied to the subscription
            $table->string('code'); // Unique code for subscription identification and auto generation
            $table->string('customer_name'); // Name of the customer subscribing
            $table->string('customer_email'); // Email of the customer subscribing
            $table->string('price_type')->default('per_user'); // per_location or per_user
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, etc.
            $table->integer('quantity')->default(1); // Number of users or locations subscribed
            $table->integer('length_of_term')->default(1); // Length of the subscription term in billing cycles
            $table->date('start_date'); // Start date of the subscription
            $table->date('end_date'); // End date of the subscription
            $table->double('tax_percentage')->default(0); // Tax percentage applied to the product
            $table->double('price')->default(0); // Price at the time of subscription
            $table->double('tax')->default(0); // Tax applied to the subscription
            $table->string('discount_type')->nullable(); // percentage or fixed
            $table->double('discount')->default(0); // Discount applied to the subscription
            $table->double('subtotal')->default(0); // Subtotal before tax and discount (quantity * length_of_term * price)
            $table->double('total')->default(0); // Total price (quantity * length_of_term * [price + tax] - discount)
            $table->double('agent_commission')->default(0); // Commission earned by the agent for this subscription (commission_rate * subtotal)
            $table->string('payment_status')->default('pending'); // pending, paid, failed, etc.
            $table->string('subscription_status')->default('active'); // active, cancelled, and expired.
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_subscription', function (Blueprint $table) {
            $table->foreignIdFor(Product::class)->constrained()->onDelete('cascade'); // Foreign key to products table
            $table->foreignIdFor(Subscription::class)->constrained()->onDelete('cascade'); // Foreign key to subscriptions table
            $table->primary(['product_id', 'subscription_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_subscription');
        Schema::dropIfExists('subscriptions');
    }
};
