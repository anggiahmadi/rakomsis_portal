<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Withdrawal extends Model
{
    /** @use HasFactory<\Database\Factories\WithdrawalFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'amount',
        'withdrawal_status',
        'requested_at',
        'processed_at'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'withdrawal_status' => WithdrawalStatus::class,
    ];

    public function agent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function approve()
    {
        $this->withdrawal_status = WithdrawalStatus::Approved;

        $this->processed_at = now();

        ($this->save()) ? $this->agent->additionalWithdrawn(-$this->amount) : null;
    }

    public function reject()
    {
        $this->withdrawal_status = WithdrawalStatus::Rejected;

        $this->processed_at = now();

        ($this->save()) ? $this->agent->additionalPendingWithdrawn(-$this->amount) : null;
    }

    public function isPending(): bool
    {
        return $this->withdrawal_status === WithdrawalStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->withdrawal_status === WithdrawalStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->withdrawal_status === WithdrawalStatus::Rejected;
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
