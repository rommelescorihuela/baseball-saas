<?php

namespace App\Enums;

enum Plan: string
{
    case FREE = 'free';
    case PRO = 'pro';
    case UNLIMITED = 'unlimited';

    public function maxCompetitions(): ?int
    {
        return match ($this) {
            self::FREE => 1,
            self::PRO => 5,
            self::UNLIMITED => null, // null means unlimited
        };
    }

    public function maxTeams(): ?int
    {
        return match ($this) {
            self::FREE => 8,
            self::PRO => 20,
            self::UNLIMITED => null,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::FREE => 'Plan Gratuito',
            self::PRO => 'Plan Pro',
            self::UNLIMITED => 'Plan Unlimited',
        };
    }

    public function stripePriceId(): ?string
    {
        return match ($this) {
            self::FREE => null,
            self::PRO => env('STRIPE_PRICE_PRO'),
            self::UNLIMITED => env('STRIPE_PRICE_UNLIMITED'),
        };
    }

    public static function fromPriceId(string $priceId): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->stripePriceId() === $priceId) {
                return $case;
            }
        }
        return null;
    }
}