<?php

namespace App\Enums;

enum PaymentPurpose: string
{
    case SubscriptionPayment = 'subscription_payment';
    case CommissionPayout = 'commission_payout';
}
