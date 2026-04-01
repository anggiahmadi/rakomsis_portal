<?php

use App\Models\Agent;
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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class)->constrained()->onDelete('cascade'); // Each withdrawal belongs to an agent
            $table->double('amount', 15, 2); // Amount withdrawn
            $table->string('withdrawal_status')->default('pending'); // pending, approved, rejected
            $table->timestamp('requested_at')->useCurrent(); // When the withdrawal was requested
            $table->timestamp('processed_at')->nullable(); // When the withdrawal was processed (approved
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
