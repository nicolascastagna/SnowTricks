<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly string $userDirectory
    ) {
    }

    #[Route('/{username}', name: 'app_profile_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{username}/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
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

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{username}', name: 'app_profile_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getUsername(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Uploads an image and returns the new file name
     *
     * @param UploadedFile $image   
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

    private function deleteUserPicture(string $fileName): void
    {
        $filePath = $this->userDirectory . '/' . $fileName;
        if (file_exists($filePath)) {
            try {
                unlink($filePath);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression de l\'ancienne image.');
            }
        }
    }
}
