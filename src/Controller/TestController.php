<?php

namespace App\Controller;

use App\Entity\Pizza;
use App\Enum\IngredientsEnum;
use App\Enum\SizeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(EntityManagerInterface $em): Response
    {

        $pizza = new Pizza(
            SizeEnum::LARGE,
            [IngredientsEnum::CHEESE, IngredientsEnum::TOMATO, IngredientsEnum::PEPPERONI]
        );

        $em->persist($pizza);
        $em->flush();

        dd('Hola mundo');
    }


    /* return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);*/
}
