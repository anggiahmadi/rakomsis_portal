<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CreditCard = 'credit_card';
    case BankTransfer = 'bank_transfer';
    case EWallet = 'e_wallet';
    case Qris = 'qris';

    public function description(): string
    {
        return match ($this) {
            self::CreditCard => 'Payment made using a credit card.',
            self::BankTransfer => 'Payment made through a bank transfer.',
            self::EWallet => 'Payment made using an electronic wallet (e-wallet).',
            self::Qris => 'Payment made using QRIS (Quick Response Code Indonesian Standard).',
        };
    }
}
