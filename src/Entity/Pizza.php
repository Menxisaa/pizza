<?php

namespace App\Entity;

use App\Enum\BaseEnum;
use App\Enum\IngredientsEnum;
use App\Enum\SizeEnum;
use App\Repository\PizzaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PizzaRepository::class)]
class Pizza
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 55)]
    private ?string $name = null;

    #[ORM\Column(length: 55)]
    private ?string $size = null;

    #[ORM\Column(length: 55)]
    private ?string $base = null;

    #[ORM\Column]
    private array $ingredients = [];

    #[ORM\Column(type: 'integer')]
    private int $priceInCents = 0;


    public function __construct(array $ingredients = [])
    {
        $this->name = 'Pizza';
        $this->ingredients = array_map(fn($i) => $i->value, $ingredients);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSize(): ?SizeEnum
    {
        return SizeEnum::from($this->size);
    }

    public function setSize(SizeEnum $size): self
    {
        $this->size = $size->value;

        return $this;
    }

    public function getBase(): ?BaseEnum
    {
        return BaseEnum::from($this->base);
    }

    public function setBase(?BaseEnum $base): self
    {
        $this->base = $base->value;

        return $this;
    }


    /**
     * @return IngredientsEnum[]
     */
    public function getIngredients(): array
    {
        return array_map(
            fn(string $ingredient) => IngredientsEnum::from($ingredient),
            $this->ingredients
        );
    }

    /**
     * @param IngredientsEnum[] $ingredients
     */
    public function setIngredients(array $ingredients): self
    {
        $this->ingredients = array_map(fn($i) => $i->value, $ingredients);
        return $this;
    }

    public function addIngredient(IngredientsEnum $ingredient): self
    {
        if (!in_array($ingredient->value, $this->ingredients)) {
            $this->ingredients[] = $ingredient->value;
        }
        return $this;
    }

    public function removeIngredient(IngredientsEnum $ingredient): self
    {
        $this->ingredients = array_filter(
            $this->ingredients,
            fn($i) => $i !== $ingredient->value
        );
        return $this;
    }

    // Métodos para trabajar en euros y céntimos
    public function getPriceInCents(): int
    {
        return $this->priceInCents;
    }

    public function getPriceInEuros(): float
    {
        return $this->priceInCents / 100;
    }

    public function setPriceInCents(int $cents): void
    {
        $this->priceInCents = $cents;
    }

    public function setPriceInEuros(float $euros): void
    {
        $this->priceInCents = (int) round($euros * 100);
    }

}
