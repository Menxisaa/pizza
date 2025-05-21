<?php

// src/Enum/SizeEnum.php
namespace App\Enum;

enum SizeEnum: string
{
    case SMALL = 'small';
    case MEDIUM = 'medium';
    case LARGE = 'large';

    public const VALID_SIZES = [
        self::SMALL->value,
        self::MEDIUM->value,
        self::LARGE->value,
    ];

    public function label(): string
    {
        return match ($this) {
            self::SMALL => 'Pequeña',
            self::MEDIUM => 'Mediana',
            self::LARGE => 'Grande'
        };
    }


    public function getCost(): float
    {
        return match ($this) {
            self::MEDIUM => 1495,
            self::LARGE => 1995,
            default => 995,
        };
    }
}
