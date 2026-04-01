<?php

namespace App\Enums;

enum ProductType: string
{
    case Single = 'single';
    case Bundle = 'bundle';

    public function description(): string
    {
        return match ($this) {
            self::Single => 'A standalone product',
            self::Bundle => 'A product that includes other products',
        };
    }
}
