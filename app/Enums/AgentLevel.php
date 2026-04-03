<?php

namespace App\Enums;

enum AgentLevel: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';

    public function nextLevel(): ?self
    {
        return match ($this) {
            self::Bronze => self::Silver,
            self::Silver => self::Gold,
            self::Gold => null, // Gold is the highest level
        };
    }

    public function discountRate(): float
    {
        return match ($this) {
            self::Bronze => 0.05, // 5% discount for Bronze level
            self::Silver => 0.10, // 10% discount for Silver level
            self::Gold => 0.15, // 15% discount for Gold level
        };
    }

    public function commissionRate(): float
    {
        return match ($this) {
            self::Bronze => 0.02, // 2% commission for Bronze level
            self::Silver => 0.05, // 5% commission for Silver level
            self::Gold => 0.10, // 10% commission for Gold level
        };
    }

    public function salesThreshold(): float
    {
        return match ($this) {
            self::Bronze => 0, // No threshold for Bronze level
            self::Silver => 100000000, // IDR 100,000,000 total sales to reach Silver level
            self::Gold => 1000000000, // IDR 1,000,000,000 total sales to reach Gold level
        };
    }

    public function canUpgrade(float $totalSales): bool
    {
        return $totalSales >= $this->salesThreshold();
    }

    public function upgradeLevel(float $totalSales): self
    {
        if ($this->canUpgrade($totalSales)) {
            return $this->nextLevel() ?? $this; // Upgrade to next level if possible, otherwise stay at current level
        }
        return $this; // No upgrade if sales threshold is not met
    }

    public function description(): string
    {
        return match ($this) {
            self::Bronze => 'Bronze level agents earn a 2% commission and provide a 5% discount to their customers.',
            self::Silver => 'Silver level agents earn a 5% commission and provide a 10% discount to their customers.',
            self::Gold => 'Gold level agents earn a 10% commission and provide a 15% discount to their customers.',
        };
    }

    public function benefits(): string
    {
        return match ($this) {
            self::Bronze => 'Bronze agents receive basic support and access to promotional materials.',
            self::Silver => 'Silver agents receive priority support, access to exclusive promotions, and higher commission rates.',
            self::Gold => 'Gold agents receive dedicated account management, early access to new products, and the highest commission rates.',
        };
    }

    public function benefitsArray(): array
    {
        return match ($this) {
            self::Bronze => [
                'commission_rate' => $this->commissionRate(),
                'discount_rate' => $this->discountRate(),
                'support_level' => 'Basic Support',
                'sales_threshold' => $this->salesThreshold(),
            ],
            self::Silver => [
                'commission_rate' => $this->commissionRate(),
                'discount_rate' => $this->discountRate(),
                'support_level' => 'Priority Support',
                'sales_threshold' => $this->salesThreshold(),
            ],
            self::Gold => [
                'commission_rate' => $this->commissionRate(),
                'discount_rate' => $this->discountRate(),
                'support_level' => 'Dedicated Account Management',
                'sales_threshold' => $this->salesThreshold(),
            ],
        };
    }

    public function requirements(): string
    {
        return match ($this) {
            self::Bronze => 'No specific requirements to become a Bronze agent.',
            self::Silver => 'To become a Silver agent, you need to achieve total sales of IDR 100,000,000.',
            self::Gold => 'To become a Gold agent, you need to achieve total sales of IDR 1,000,000,000.',
        };
    }

    public function isEligibleForUpgrade(float $totalSales): bool
    {
        return $this->canUpgrade($totalSales);
    }

    public function nextLevelBenefits(): string
    {
        $nextLevel = $this->nextLevel();
        return $nextLevel ? $nextLevel->benefits() : 'You are at the highest level and cannot upgrade further.';
    }

    public function nextLevelDescription(): string
    {
        $nextLevel = $this->nextLevel();
        return $nextLevel ? $nextLevel->description() : 'You are at the highest level and cannot upgrade further.';
    }

    public function nextLevelRequirements(): string
    {
        $nextLevel = $this->nextLevel();
        return $nextLevel ? $nextLevel->requirements() : 'You are at the highest level and cannot upgrade further.';
    }

    public function nextLevelCommissionRate(): float
    {
        $nextLevel = $this->nextLevel();
        return $nextLevel ? $nextLevel->commissionRate() : $this->commissionRate();
    }

    public function nextLevelDiscountRate(): float
    {
        $nextLevel = $this->nextLevel();
        return $nextLevel ? $nextLevel->discountRate() : $this->discountRate();
    }

    public function nextLevelSalesThreshold(): float
    {
        $nextLevel = $this->nextLevel();
        return $nextLevel ? $nextLevel->salesThreshold() : $this->salesThreshold();
    }

    public function nextLevelUpgradeEligibility(float $totalSales): bool
    {
        $nextLevel = $this->nextLevel();
        return $nextLevel ? $nextLevel->canUpgrade($totalSales) : false;
    }
}
