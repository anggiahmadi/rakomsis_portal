<?php

namespace App\Enums;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED_AMOUNT = 'fixed_amount';

    public function description(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Discount is a percentage of the original price.',
            self::FIXED_AMOUNT => 'Discount is a fixed amount subtracted from the original price.',
        };
    }
}
