<?php

use App\Models\Subscription;
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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('provisioning_status')->default('pending')->after('business_type');
            $table->timestamp('provisioned_at')->nullable()->after('provisioning_status');
            $table->text('provisioning_error')->nullable()->after('provisioned_at');
            $table->string('cloudflare_record_id')->nullable()->after('provisioning_error');
            $table->string('frontend_path')->nullable()->after('cloudflare_record_id');
            $table->unsignedInteger('provisioning_attempts')->default(0)->after('frontend_path');
            $table->foreignIdFor(Subscription::class, 'last_provisioned_subscription_id')
                ->nullable()
                ->after('provisioning_attempts')
                ->constrained('subscriptions')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('last_provisioned_subscription_id');
            $table->dropColumn([
                'provisioning_status',
                'provisioned_at',
                'provisioning_error',
                'cloudflare_record_id',
                'frontend_path',
                'provisioning_attempts',
            ]);
        });
    }
};
