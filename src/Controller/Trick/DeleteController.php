<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository $trickRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly PictureRepository $pictureRepository
    ) {
    }

    #[Route('/trick/{id}/remove-image/{imageName}', name: 'app_trick_remove_image', methods: [Request::METHOD_GET])]
    #[IsGranted('ROLE_USER')]
    public function removeImage(Trick $trick, $imageName = null): Response
    {
        if ($imageName) {
            $image = $this->pictureRepository->findOneBy(['name' => $imageName]);
            if (!$image) {
                $this->addFlash('error', 'Image non trouvée.');
                return $this->redirectToRoute('app_trick_edit', ['id' => $trick->getId()]);
            }
            $this->deleteImage($image->getName());
            if ($image->getName() === $trick->getMainImage()) {
                $trick->setMainImage('image-placeholder.jpg');
            }
            $trick->removePicture($image);
            $this->entityManager->remove($image);
        } else {
            if ($trick->getMainImage() !== 'image-placeholder.jpg') {
                $this->deleteImage($trick->getMainImage());
                //remove same image
                foreach ($trick->getPictures() as $picture) {
                    if ($picture->getName() === $trick->getMainImage()) {
                        $trick->removePicture($picture);
                        $this->entityManager->remove($picture);
                        break;
                    }
                }
                $trick->setMainImage('image-placeholder.jpg');
            }
        }

        $this->entityManager->flush();
        $this->addFlash('success', 'Image supprimée avec succès.');

        return $this->redirectToRoute('app_trick_edit', [
            '_fragment' => 'existing-images',
            'id' => $trick->getId()
        ]);
    }

    #[Route('/{id}', name: 'app_trick_delete', methods: Request::METHOD_POST)]
    public function delete(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            foreach ($trick->getPictures() as $picture) {
                $this->deleteImage($picture->getName());
                $this->entityManager->remove($picture);
            }
            if ($trick->getMainImage() && $trick->getMainImage() !== 'image-placeholder.jpg') {
                $this->deleteImage($trick->getMainImage());
            }
            $entityManager->remove($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Le trick a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression du trick.');
        }

        return $this->redirectToRoute('homepage', [
            '_fragment' => 'tricks-container'
        ], Response::HTTP_SEE_OTHER);
    }

    private function deleteImage(string $imageName): void
    {
        $imagePath = $this->getParameter('tricks_directory') . '/' . $imageName;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}
