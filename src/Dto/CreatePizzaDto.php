<?php

namespace App\Dto;

use App\Enum\IngredientsEnum;
use App\Enum\SizeEnum;
use App\Enum\BaseEnum;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePizzaDto
{
    public const FIELD_SIZE = 'size';
    public const FIELD_INGREDIENTS = 'ingredients';
    public const FIELD_BASE_TYPE = 'baseType';


    /************************************************************************************/
    /*                  VALIDACIONES DE CAMPOS DE ENTRADA                               */
    /************************************************************************************/


    #[Assert\NotBlank]
    #[Assert\Choice(choices: SizeEnum::VALID_SIZES)]
    public string $size;

    /**
     * @var string[]
     */
    #[Assert\NotBlank]
    #[Assert\Count(
        min: 2,
        max: 4,
        minMessage: "Debes seleccionar al menos {{ limit }} ingredientes.",
        maxMessage: "No puedes seleccionar más de {{ limit }} ingredientes."
    )]
    #[Assert\Unique(message: "Los ingredientes no pueden estar repetidos.")]
    #[Assert\All([new Assert\Choice(callback: [IngredientsEnum::class, 'getValues'])])]
    public array $ingredients = [];

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [BaseEnum::class, 'validValues'])]
    public string $baseType;
}