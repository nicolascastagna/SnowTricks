<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly string $userDirectory
    ) {
    }

    #[Route('/{username}', name: 'app_profile_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(User $user): Response
    {
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{username}/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $originalUserPicture = $user->getUserPicture();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userPicture = $form->get('userPicture')->getData();
            if ($userPicture) {
                $fileName = $this->uploadImage($userPicture);

                if ($originalUserPicture && $originalUserPicture !== $fileName) {
                    $this->deleteUserPicture($originalUserPicture);
                }
                $user->setUserPicture($fileName);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_profile_show', [
                'username' => $user->getUsername()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profile_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->get('_token'))) {
            if ($user->getUserPicture()) {
                $this->deleteUserPicture($user->getUserPicture());
            }
            $entityManager->remove($user);
            $entityManager->flush();

            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);

            return $this->redirectToRoute('app_logout');
        }

        return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Uploads an image and returns the new file name
     *
     * @param UploadedFile $image   
     *
     * @return string
     */
    private function uploadImage(UploadedFile $image): string
    {
        $fileName = md5(uniqid()) . '.' . $image->guessExtension();

        try {
            $image->move($this->userDirectory, $fileName);
        } catch (FileException $e) {
            throw new HttpException('Une erreur est survenue lors de l\'upload de l\'image.', $e);
        }

        return $fileName;
    }

    /**
     * deleteUserPicture
     *
     * @param  string $fileName
     */
    private function deleteUserPicture(string $fileName): void
    {
        $filePath = $this->userDirectory . '/' . $fileName;
        if (file_exists($filePath)) {
            try {
                unlink($filePath);
            } catch (Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression de l\'ancienne image.');
            }
        }
    }
}
