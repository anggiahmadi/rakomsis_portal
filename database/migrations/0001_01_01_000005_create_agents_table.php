<?php

use App\Models\User;
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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->unique()->constrained();
            $table->string('code')->unique(); // Unique code for agent identification and auto generation
            $table->string('bank_name')->nullable(); // Bank name for commission payouts
            $table->string('bank_account_number')->nullable(); // Bank account number for commission payouts
            $table->string('level')->default('bronze'); // Agent level for commission rates
            $table->double('commission_rate')->default(0.02); // Commission rate based on agent level
            $table->double('discount_rate')->default(0.05); // Discount rate for customers referred by the agent
            $table->double('total_sales')->default(0); // Total sales for the agent
            $table->double('total_commission')->default(0); // Total commission earned by the agent
            $table->double('balance')->default(0); // Current balance of the agent
            $table->double('withdrawn')->default(0); // Total amount withdrawn by the agent
            $table->double('pending_withdrawal')->default(0); // Amount pending withdrawal
            $table->boolean('is_active')->default(true); // Whether the agent is active or not
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
