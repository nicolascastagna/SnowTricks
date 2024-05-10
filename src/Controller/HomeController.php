<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    public function __construct(private readonly TrickRepository $trickRepository)
    {
    }

    #[Route('/', name: 'homepage', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        $tricks = $this->trickRepository->findAll();

        return $this->render('homepage.html.twig', [
            'tricks' => $tricks,
        ]);
    }
}
