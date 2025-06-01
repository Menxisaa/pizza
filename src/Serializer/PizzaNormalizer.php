<?php

namespace App\Serializer;

use App\Entity\Pizza;
use App\Enum\HasExtraCostInterface;
use App\Enum\IngredientsEnum;
use App\Enum\LabeledEnumInterface;
use App\Enum\SizeEnum;
use BackedEnum;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

readonly class PizzaNormalizer implements NormalizerInterface
{
    public function __construct(
        private ObjectNormalizer $objectNormalizer
    ) {}

    public function normalize($pizza, string $format = null, array $context = []): array
    {
        // Normalización básica con ObjectNormalizer
        $data = $this->objectNormalizer->normalize($pizza, $format, $context);


        // Transformar Enums a estructuras
        $data['size'] = $this->formatEnum($pizza->getSize());
        $data['base'] = $this->formatEnum($pizza->getBase());
        $data['ingredients'] = array_map(
            fn(IngredientsEnum $ingredient) => $this->formatEnumIngredients($ingredient),
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

    private function formatEnum(BackedEnum $enum): array
    {
        $extraPrice = ($enum instanceof HasExtraCostInterface) ? $enum->extraCost() : 0;

        $isSize = $enum instanceof SizeEnum;

        return [
            'value' => $enum->value,
            'label' => $enum instanceof LabeledEnumInterface ? $enum->label() : $enum->value,
            $isSize ? 'base_price' : 'extra_cost' => $extraPrice,
            $isSize ? 'base_price_in_euro' : 'extra_cost_in_euro' => $extraPrice ? $extraPrice / 100 : 0.00,
        ];
    }

    private function formatEnumIngredients(IngredientsEnum $enum): array
    {
        return [
            'value' => $enum->value,
            'label' => method_exists($enum, 'label') ? $enum->label() : $enum->value
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Pizza;
    }
}