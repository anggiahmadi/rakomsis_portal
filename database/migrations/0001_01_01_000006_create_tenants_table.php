<?php

use App\Models\Customer;
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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Unique code for tenant identification and auto generation
            $table->string('domain')->unique(); // Unique domain for tenant access (e.g., tenant1.rakomsis.com)
            $table->string('name'); // Name of the tenant
            $table->string('address')->nullable(); // Address of the tenant
            $table->string('business_type')->nullable(); // Type of business the tenant operates
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customer_tenant', function (Blueprint $table) {
            $table->foreignIdFor(Customer::class)->constrained()->onDelete('cascade'); // Foreign key to customers table
            $table->foreignIdFor(Tenant::class)->constrained()->onDelete('cascade'); // Foreign key to tenants table
            $table->string('role')->default('owner'); // Role of the customer in the tenant (e.g., owner, admin, user)
            $table->primary(['customer_id', 'tenant_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
