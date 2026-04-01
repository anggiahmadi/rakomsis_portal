<?php

namespace App\Enums;

enum PaymentPurpose: string
{
    case SUBSCRIPTION_PAYMENT = 'subscription_payment';
    case COMMISSION_PAYOUT = 'commission_payout';
}
