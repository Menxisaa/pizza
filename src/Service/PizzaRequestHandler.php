<?php

namespace App\Service;

use App\Dto\CreatePizzaDto;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PizzaRequestHandler
{
    public function __construct(private ValidatorInterface $validator) {}

    public function handleRequest(array $data): CreatePizzaDto|array
    {
        $dto = new CreatePizzaDto();
        $dto->size = $data[CreatePizzaDto::FIELD_SIZE] ?? '';
        $dto->ingredients = $data[CreatePizzaDto::FIELD_INGREDIENTS] ?? [];
        $dto->baseType = $data[CreatePizzaDto::FIELD_BASE_TYPE] ?? '';

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->formatErrors($errors);
        }

        return $dto;
    }

    private function formatErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        return $errorMessages;
    }
}