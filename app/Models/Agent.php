<?php

namespace App\Models;

use App\Enums\AgentLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    /** @use HasFactory<\Database\Factories\AgentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'code',
        'bank_name',
        'bank_account_number',
        'level',
        'discount_rate',
        'commission_rate',
        'total_sales',
        'total_commission',
        'balance',
        'withdrawn',
        'pending_withdrawal',
        'is_active',
    ];

    protected $casts = [
        'level' => AgentLevel::class,
        'is_active' => 'boolean',
        'discount_rate' => 'double',
        'commission_rate' => 'double',
        'total_sales' => 'double',
        'total_commission' => 'double',
        'balance' => 'double',
        'withdrawn' => 'double',
        'pending_withdrawal' => 'double',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function withdrawals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }
}
