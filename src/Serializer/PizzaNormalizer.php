<?php

namespace App\Serializer;

use App\Entity\Pizza;
use App\Enum\BaseEnum;
use App\Enum\IngredientsEnum;
use App\Enum\SizeEnum;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PizzaNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly ObjectNormalizer $objectNormalizer
    ) {}

    public function normalize($pizza, string $format = null, array $context = []): array
    {
        // Normalización básica con ObjectNormalizer
        $data = $this->objectNormalizer->normalize($pizza, $format, $context);


        // Transformar Enums a estructuras ricas
        $data['size'] = $this->formatEnum($pizza->getSize()->value, SizeEnum::class);
        $data['base'] = $this->formatEnum($pizza->getBase()->value, BaseEnum::class);
        $data['ingredients'] = array_map(
            fn(IngredientsEnum $ingredient) => $this->formatEnum($ingredient->value, IngredientsEnum::class),
            $pizza->getIngredients()
        );

        // Formatear precio
        $data['price'] = [
            'cents' => $pizza->getPriceInCents(),
            'euros' => $pizza->getPriceInEuros(),
            'formatted' => number_format($pizza->getPriceInEuros(), 2, ',', ' ') . ' €'
        ];

        return $data;
    }

    private function formatEnum(string $value, string $enumClass): array
    {
        $enum = $enumClass::tryFrom($value);

        return [
            'value' => $value,
            'label' => $enum?->label() ?? $value,
            'extra_cost' => method_exists($enum, 'extraCost') ? $enum->extraCost() : 0
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Pizza && $format === 'json';
    }
}