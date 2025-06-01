<?php

namespace App\Provider;

use App\Enum\BaseEnum;
use App\Enum\HasExtraCostInterface;
use App\Enum\IngredientsEnum;
use App\Enum\LabeledEnumInterface;
use App\Enum\SizeEnum;
use BackedEnum;

readonly class PizzaEnumProvider
{
    public function getPizzaEnums(): array
    {
        return [
            'sizes' => $this->formatEnumList(SizeEnum::cases()),
            'bases' => $this->formatEnumList(BaseEnum::cases()),
            'ingredients' => $this->formatEnumList(IngredientsEnum::cases()),
        ];
    }

    private function formatEnumList(array $enums): array
    {
        return array_map(function (BackedEnum $enum) {
            return [
                'value' => $enum->value,
                'label' => $enum instanceof LabeledEnumInterface ? $enum->label() : $enum->value,
                'extra_cost' => $enum instanceof HasExtraCostInterface ? $enum->extraCost() : 0,
            ];
        }, $enums);
    }
}
