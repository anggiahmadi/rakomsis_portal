<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case NotPaid = 'not_paid';
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::NotPaid => 'Not Paid',
            self::Pending => 'Pending',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Refunded => 'Refunded',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NotPaid => 'Payment has not been made yet.',
            self::Pending => 'Payment is pending and awaiting processing.',
            self::Completed => 'Payment has been successfully completed.',
            self::Failed => 'Payment failed due to an error or insufficient funds.',
            self::Refunded => 'Payment has been refunded to the customer.',
        };
    }
}
