<?php

namespace App\Controller;

use App\Dto\CreatePizzaDto;
use App\Entity\Pizza;
use App\Enum\IngredientsEnum;
use App\Enum\BaseEnum;
use App\Enum\SizeEnum;
use App\Form\PizzaForm;
use App\Repository\PizzaRepository;
use App\Service\PizzaCreatorService;
use App\Service\PizzaRequestHandler;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use NumberFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/pizza')]
final class PizzaController extends AbstractController
{
    #[Route(name: 'app_pizza_index', methods: ['GET'])]
    public function index(PizzaRepository $pizzaRepository): Response
    {
        return $this->render('pizza/index.html.twig', [
            'pizzas' => $pizzaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pizza_new', methods: ['POST'])]
    public function new(
        Request             $request,
        PizzaRequestHandler $requestHandler,
        PizzaCreatorService $pizzaCreator
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $result = $requestHandler->handleRequest($data);

        if (is_array($result)) {
            return new JsonResponse(['errors' => $result], Response::HTTP_BAD_REQUEST);
        }

        $pizza = $pizzaCreator->create($result); // Lógica de negocio separada

        return new JsonResponse(['id' => $pizza->getId()], Response::HTTP_CREATED);
    }


    #[Route('/{id}', name: 'app_pizza_show', methods: ['GET'])]
    public function show(Pizza $pizza, SerializerInterface $serializer): JsonResponse
    {
        try {
            // 1. Construir el array de datos estructurados
            $responseData = [
                'id' => $pizza->getId(),
                'size' => $this->formatEnum($pizza->getSize()->value, SizeEnum::class), // Size es un Enum
                'base' => $this->formatEnum($pizza->getBase()->value, BaseEnum::class), // Base es un Enum
                'ingredients' => array_map(
                    fn(IngredientsEnum $ingredient) => $this->formatEnum($ingredient->value, IngredientsEnum::class),
                    $pizza->getIngredients()
                ),

                'price' => [
                    'cents' => $pizza->getPriceInCents(),
                    'euros' => $pizza->getPriceInEuros(),
                    'formatted' => $this->formatPrice($pizza->getPriceInEuros())
                ],
                'metadata' => [
                    /*'created_at' => $pizza->getCreatedAt()?->format(\DateTimeInterface::ATOM),
                    'updated_at' => $pizza->getUpdatedAt()?->format(\DateTimeInterface::ATOM)*/
                ]
            ];

            // 2. Serializar con grupos de normalización (opcional)
            $context = [
                'groups' => ['pizza:read'],
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_PRETTY_PRINT
            ];

            return new JsonResponse(
                $serializer->serialize($responseData, 'json', $context),
                Response::HTTP_OK,
                [],
                true
            );

        } catch (\Throwable $e) {
            // 3. Manejo centralizado de errores (usaría un EventSubscriber en producción)
            return new JsonResponse(
                ['error' => 'Failed to process pizza data: ' .  $e],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    // Helper para formatear Enums
    private function formatEnum(string $value, string $enumClass): array
    {
        // Verificar si la clase es un Enum válido
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException("$enumClass is not a valid enum class");
        }

        // Crear un reflejo para acceder a los métodos estáticos
        $reflection = new \ReflectionEnum($enumClass);

        // Versión segura para cualquier PHP 8.1+ (sin depender de tryFrom)
        $cases = $reflection->getCases();
        foreach ($cases as $case) {
            if ($case->getValue()->value === $value) {
                $enum = $case->getValue();
                return [
                    'value' => $value,
                    'label' => method_exists($enum, 'label') ? $enum->label() : $value,
                    'extra_cost' => method_exists($enum, 'extraCost') ? $enum->extraCost() : 0
                ];
            }
        }

        // Valor no encontrado en el Enum
        return [
            'value' => $value,
            'label' => 'Desconocido',
            'extra_cost' => 0
        ];
    }

    // Helper para formatear precio
    private function formatPrice(float $price): string
    {
        $formatter = new NumberFormatter('es_ES', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($price, 'EUR');
    }

    #[Route('/{id}/edit', name: 'app_pizza_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pizza $pizza, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PizzaForm::class, $pizza);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pizza_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pizza/edit.html.twig', [
            'pizza' => $pizza,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pizza_delete', methods: ['POST'])]
    public function delete(Request $request, Pizza $pizza, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pizza->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pizza);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pizza_index', [], Response::HTTP_SEE_OTHER);
    }
}
