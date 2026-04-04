<?php

namespace App\Models;

use App\Enums\BillingCycle;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'price_per_location',
        'price_per_user',
        'tax_percentage',
        'billing_cycle',
        'product_type',
    ];

    protected $casts = [
        'price_per_location' => 'double',
        'price_per_user' => 'double',
        'billing_cycle' => BillingCycle::class,
        'product_type' => ProductType::class,
    ];

    public function includedProducts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_includes', 'main_product_id', 'included_product_id')->withTimestamps();
    }

    public function mainProducts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_includes', 'included_product_id', 'main_product_id')->withTimestamps();
    }

    public function promotions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'product_promotion', 'product_id', 'promotion_id')->withTimestamps();
    }
}
