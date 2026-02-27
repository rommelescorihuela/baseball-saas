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

    public function maxCategories(): ?int
    {
        return match ($this) {
            self::FREE => 1,
            self::PRO => 5,
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


}