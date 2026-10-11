<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\FiltersType;

class HomeController extends AbstractController
{
    #[Route("/", name: "home")]
    public function index(): Response
    {
        return $this->render('pages/index.html.twig');
    }

    public function filter(): Response
    {
        $form = $this->createForm(FiltersType::class);
        return $this->render('components/_sidebar.html.twig', [
            'form' => $form
        ]);
    }
}