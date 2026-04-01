<?php

namespace App\Enums;

enum BillingCycle: string
{
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public function description(): string
    {
        return match ($this) {
            self::Monthly => 'Billed every month',
            self::Yearly => 'Billed every year',
        };
    }
}
