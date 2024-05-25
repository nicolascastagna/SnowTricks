<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Form\TrickFormType;
use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use App\Service\TrickService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository $trickRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TrickService $trickService,
        private readonly PictureRepository $pictureRepository
    ) {
    }

    #[Route('/trick/{id}/edit', name: 'app_trick_edit')]
    public function edit(Request $request, Trick $trick): Response
    {
        $form = $this->createForm(TrickFormType::class, $trick);
        $video = $trick->getVideos();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdateDate(new \DateTime());
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

            $this->addFlash('success', 'Le trick a bien été modifié !');

            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'videos' => $video,
        ]);
    }
}
