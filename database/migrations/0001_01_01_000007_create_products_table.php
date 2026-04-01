<?php

use App\Models\Product;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Unique code for agent identification and auto generation
            $table->string('name'); // Name of the product
            $table->text('description')->nullable(); // Description of the product
            $table->double('price_per_location')->default(0); // Price per location for the product
            $table->double('price_per_user')->default(0); // Price per user for the product
            $table->double('tax_percentage')->default(0); // Tax percentage applied to the product
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, etc.
            $table->string('product_type')->default('single'); // single or bundle
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_includes', function (Blueprint $table) {
            $table->foreignIdFor(Product::class, 'main_product_id')->constrained()->onDelete('cascade');
            $table->foreignIdFor(Product::class, 'included_product_id')->constrained('products', 'id')->onDelete('cascade');
            $table->primary(['main_product_id', 'included_product_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
