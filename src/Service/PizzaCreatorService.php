<?php

namespace App\Service;

use App\Dto\CreatePizzaDto;
use App\Entity\Pizza;
use App\Enum\BaseEnum;
use App\Enum\IngredientsEnum;
use App\Enum\SizeEnum;
use App\Repository\PizzaRepository;
use Exception;
use InvalidArgumentException;

readonly class PizzaCreatorService
{
    public function __construct(private PizzaRepository $pizzaRepository)
    {
    }

    public function create(CreatePizzaDto $dto): Pizza
    {
        // Validación básica
        if (empty($dto->ingredients)) {
            throw new InvalidArgumentException('At least one ingredient is required');
        }

        // Conversión con manejo de errores
        try {
            $size = SizeEnum::from($dto->size);
            $base = BaseEnum::from($dto->baseType);
            $ingredients = array_map(
                fn(string $ingredient) => IngredientsEnum::from($ingredient),
                $dto->ingredients
            );
        } catch (Exception $e) {
            throw new InvalidArgumentException('Invalid enum value: ' . $e->getMessage());
        }

        $pizza = new Pizza($ingredients);
        $pizza->setSize($size);
        $pizza->setBase($base);

        $this->pizzaRepository->savePizza($pizza);

        return $pizza;
    }
}