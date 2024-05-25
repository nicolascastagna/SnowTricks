<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository $trickRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly PictureRepository $pictureRepository
    ) {
    }

    #[Route('/trick/{id}/remove-image/{imageName}', name: 'app_trick_remove_image')]
    public function removeImage(Trick $trick, $imageName = null): Response
    {
        if ($imageName) {
            $image = $this->pictureRepository->findOneBy(['name' => $imageName]);
            if (!$image) {
                $this->addFlash('error', 'Image non trouvée.');
                return $this->redirectToRoute('app_trick_edit', ['id' => $trick->getId()]);
            }
            $this->deleteImage($image->getName());
            $trick->removePicture($image);
            $this->entityManager->remove($image);
        } else {
            $this->deleteImage($trick->getMainImage());
            $trick->setMainImage(null);
        }

        $this->entityManager->flush();
        $this->addFlash('success', 'Image supprimée avec succès.');

        return $this->redirectToRoute('app_trick_edit', ['id' => $trick->getId()]);
    }

    private function deleteImage(string $imageName): void
    {
        $imagePath = $this->getParameter('tricks_directory') . '/' . $imageName;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}
