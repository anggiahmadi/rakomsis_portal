<?php

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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Unique code for the promotion and can be used for referral or discount purposes
            $table->string('name'); // Name of the promotion
            $table->text('description')->nullable(); // Description of the promotion
            $table->date('start_date'); // Start date of the promotion
            $table->date('end_date'); // End date of the promotion
            $table->string('image')->nullable();
            $table->string('promotion_rules')->default('all'); // all, new_customers, specific_length_of_term.
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, etc. Applicable if promotion_rules is set to specific_length_of_term
            $table->integer('specific_length_of_term')->nullable(); // The specific length of term in months, applicable if promotion_rules is set to specific_length_of_term
            $table->string('discount_type')->default('percentage'); // percentage or fixed
            $table->double('discount_value')->default(0); // The value of the discount, either a percentage or a fixed amount depending on the discount_type
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
