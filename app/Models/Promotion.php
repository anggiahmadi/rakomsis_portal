<?php

namespace App\Models;

use App\Enums\BillingCycle;
use App\Enums\DiscountType;
use App\Enums\PromotionRules;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    /** @use HasFactory<\Database\Factories\PromotionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'start_date',
        'end_date',
        'image',
        'promotion_rules',
        'billing_cycle',
        'specific_length_of_term',
        'discount_type',
        'discount_value',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'promotion_rules' => PromotionRules::class,
        'billing_cycle' => BillingCycle::class,
        'specific_length_of_term' => 'integer',
        'discount_type' => DiscountType::class,
        'discount_value' => 'double',
    ];

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
