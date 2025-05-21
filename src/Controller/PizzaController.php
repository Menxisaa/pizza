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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
        Request $request,
        PizzaRequestHandler $requestHandler,
        PizzaCreatorService $pizzaCreator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $result = $requestHandler->handleRequest($data);

        if (is_array($result)) {
            return new JsonResponse(['errors' => $result], Response::HTTP_BAD_REQUEST);
        }

        $pizza = $pizzaCreator->create($result); // Lógica de negocio separada

        return new JsonResponse(['id' => $pizza->getId()], Response::HTTP_CREATED);
    }

//    public function new(Request $request, EntityManagerInterface $entityManager): Response
//    {
//        $pizza = new Pizza();
//        $form = $this->createForm(PizzaForm::class, $pizza);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($pizza);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_pizza_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('pizza/new.html.twig', [
//            'pizza' => $pizza,
//            'form' => $form,
//        ]);
//    }

    #[Route('/{id}', name: 'app_pizza_show', methods: ['GET'])]
    public function show(Pizza $pizza): Response
    {
        return $this->render('pizza/show.html.twig', [
            'pizza' => $pizza,
        ]);
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
        if ($this->isCsrfTokenValid('delete'.$pizza->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pizza);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pizza_index', [], Response::HTTP_SEE_OTHER);
    }
}
