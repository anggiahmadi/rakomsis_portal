<?php

namespace App\Models;

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
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_tenant')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function isOwner(Customer $customer)
    {
        return $this->customers()
                    ->where('customer_id', $customer->id)
                    ->wherePivot('role', 'owner')
                    ->exists();
    }
}
