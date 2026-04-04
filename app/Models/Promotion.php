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
        'has_specific_product',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'promotion_rules' => PromotionRules::class,
        'billing_cycle' => BillingCycle::class,
        'specific_length_of_term' => 'integer',
        'discount_type' => DiscountType::class,
        'discount_value' => 'double',
        'has_specific_product' => 'boolean',
    ];

    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_promotion', 'promotion_id', 'product_id')->withTimestamps();
    }

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
