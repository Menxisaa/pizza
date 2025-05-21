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
}
