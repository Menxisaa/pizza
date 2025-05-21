<?php

// src/Enum/IngredientsEnum.php
namespace App\Enum;

enum IngredientsEnum: string
{
    case CHEESE = 'cheese';
    case PEPPERONI = 'pepperoni';
    case MUSHROOM = 'mushroom';
    case OLIVES = 'olives';
    case TOMATO = 'tomato';
    case CHICKEN = 'chicken';

    public static function getValues(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

}