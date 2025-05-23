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

    public function label(): string
    {
        return match ($this) {
            self::CHEESE => 'Pequeña',
            self::PEPPERONI => 'Pepperoni',
            self::MUSHROOM => 'mushroom',
            self::OLIVES => 'Olivas',
            self::TOMATO => 'Tomate',
            self::CHICKEN => 'Pollo',
        };
    }


}