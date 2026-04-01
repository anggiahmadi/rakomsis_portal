<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'agent_id',
        'promotion_id',
        'code',
        'customer_name',
        'customer_email',
        'price_type',
        'billing_cycle',
        'quantity',
        'length_of_term',
        'start_date',
        'end_date',
        'tax_percentage',
        'price',
        'tax',
        'discount_type',
        'discount',
        'subtotal',
        'total',
        'agent_commission',
        'payment_status',
        'subscription_status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'double',
        'tax' => 'double',
        'discount' => 'double',
        'subtotal' => 'double',
        'total' => 'double',
        'agent_commission' => 'double',
        'payment_status' => PaymentStatus::class,
        'subscription_status' => SubscriptionStatus::class
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
