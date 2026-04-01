<?php

use App\Models\Subscription;
use App\Models\Withdrawal;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Subscription::class)->nullable()->constrained()->onDelete('cascade'); // Each payment belongs to a subscription
            $table->foreignIdFor(Withdrawal::class)->nullable()->constrained()->onDelete('cascade'); // Each payment can also belong to a withdrawal for commission payouts
            $table->string('payment_purpose')->default('subscription_payment'); // Purpose of the payment (subscription payment, commission payout)
            $table->double('amount'); // Amount of the payment
            $table->string('payment_method')->default('credit_card'); // Payment method used (credit_card, bank_transfer, etc.)
            $table->string('payment_status')->default('pending'); // pending, completed, failed, etc.
            $table->string('transaction_id')->nullable(); // Transaction ID from payment gateway
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
