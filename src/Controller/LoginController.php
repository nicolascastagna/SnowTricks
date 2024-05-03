<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'app_login', methods: [Request::METHOD_GET])]
    public function login(): JsonResponse
    {
        return $this->json('test');
    }
}
