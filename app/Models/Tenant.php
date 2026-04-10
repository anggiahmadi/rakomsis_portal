<?php

namespace App\Models;

use App\Enums\ProvisioningStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    /** @use HasFactory<\Database\Factories\TenantFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'domain',
        'name',
        'address',
        'business_type',
        'provisioning_status',
        'provisioned_at',
        'provisioning_error',
        'cloudflare_record_id',
        'frontend_path',
        'provisioning_attempts',
        'last_provisioned_subscription_id',
    ];

    protected $casts = [
        'provisioning_status' => ProvisioningStatus::class,
        'provisioned_at' => 'datetime',
        'provisioning_attempts' => 'integer',
    ];

    public function isOwner(Customer $customer)
    {
        return $this->customers()
                    ->where('customer_id', $customer->id)
                    ->wherePivot('role', 'owner')
                    ->exists();
    }

    public function customers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_tenant')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function lastProvisionedSubscription(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'last_provisioned_subscription_id');
    }

    public function activeSubscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class)->where('status', 'active');
    }
}
