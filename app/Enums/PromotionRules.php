<?php

namespace App\Enums;

enum PromotionRules: string
{
    case ALL = 'all';
    case NEW_CUSTOMERS = 'new_customers';
    case SPECIFIC_LENGTH_OF_TERM = 'specific_length_of_term';

    public function description(): string
    {
        return match ($this) {
            self::ALL => 'Promotion applies to all customers.',
            self::NEW_CUSTOMERS => 'Promotion applies only to new customers.',
            self::SPECIFIC_LENGTH_OF_TERM => 'Promotion applies to customers with specific length of term.',
        };
    }
}
