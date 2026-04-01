<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function description(): string
    {
        return match ($this) {
            self::Pending => 'Payment is pending and awaiting processing.',
            self::Completed => 'Payment has been successfully completed.',
            self::Failed => 'Payment failed due to an error or insufficient funds.',
            self::Refunded => 'Payment has been refunded to the customer.',
        };
    }
}
