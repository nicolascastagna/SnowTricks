<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Form\TrickFormType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddController extends AbstractController
{

    public function __construct(private readonly TrickRepository $trickRepository)
    {
    }

    #[Route('/trick/add', name: 'app_trick_add', methods: [Request::METHOD_GET])]
    /**
     * add
     *
     * @param  Trick $trick
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager,): Response
    {
        $user = new Trick();
        $form = $this->createForm(TrickFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // $entityManager->persist();
            // $entityManager->flush();

            $this->addFlash('success', 'Le trick a bien été créé !');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('trick/add.html.twig', [
            'form' => $form,
        ]);
    }
}
