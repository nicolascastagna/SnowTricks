<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Form\TrickFormType;
use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use App\Service\TrickService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EditController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository $trickRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TrickService $trickService,
        private readonly PictureRepository $pictureRepository
    ) {
    }

    #[Route('/trick/{id}/edit', name: 'app_trick_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Trick $trick): Response
    {
        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdateDate(new DateTime());
            $category = $form->get('category')->getData();
            $this->trickService->handleCategory($category, $trick);
            $mainImage = $form->get('mainImage')->getData();
            $images = $form->get('pictures')->getData();

            if ($mainImage) {
                $this->trickService->handleMainImage($mainImage, $trick);
            }

            if ($images) {
                $this->trickService->handleImages($images, $trick);
            }

            if ($trick->getMainImage() === 'image-placeholder.jpg' && !$trick->getPictures()->isEmpty()) {
                $trick->setMainImage($trick->getPictures()->first()->getName());
            }

            $this->trickService->handleVideos($trick, $form);

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le trick a bien été modifié !');

            return $this->redirectToRoute('app_trick_show', [
                '_fragment' => 'confirmation-edit',
                'id' => $trick->getId(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }
}
