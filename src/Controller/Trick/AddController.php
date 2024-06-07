<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Form\TrickFormType;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use App\Service\TrickService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AddController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository $trickRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TrickService $trickService
    ) {
    }

    #[Route('/trick/add', name: 'app_trick_add', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->trickRepository->findOneBy(['name' => $trick->getName()])) {
                $this->addFlash('error', 'Ce nom est déjà utilisé !');
                return $this->redirectToRoute('trick_add');
            }
            $trick->setCreationDate(new \DateTime());
            $trick->setUser($this->getUser());

            $category = $form->get('category')->getData();
            $this->trickService->handleCategory($category, $trick);

            $mainImage = $form->get('mainImage')->getData();
            $images = $form->get('pictures')->getData();

            $this->trickService->handleMainImage($mainImage, $trick);

            if ($images) {
                $this->trickService->handleImages($images, $trick);
            }

            $this->trickService->handleVideos($trick, $form);

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre trick a bien été créé !');

            return $this->redirectToRoute('homepage', [
                '_fragment' => 'tricks-container',
            ]);
        }

        return $this->render('trick/add.html.twig', [
            'form' => $form,
        ]);
    }
}
