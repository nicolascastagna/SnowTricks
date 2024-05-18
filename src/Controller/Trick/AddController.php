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

class AddController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository $trickRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TrickService $trickService
    ) {
    }

    #[Route('/trick/add', name: 'app_trick_add')]
    /**
     * add
     *
     * @param  Trick $trick
     * @return Response
     */
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
            $videos = $form->get('videos')->getData();

            if ($mainImage) {
                $this->trickService->handleMainImage($mainImage, $trick);
            }
            if ($images) {
                $this->trickService->handleImages($images, $trick);
            }
            if ($videos) {
                $this->trickService->handleVideos($videos, $trick);
            }

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le trick a bien été créé !');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('trick/add.html.twig', [
            'form' => $form,
        ]);
    }
}