<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        MailerService $mailerService,
        TokenGeneratorInterface $tokenGeneratorInterface
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = $tokenGeneratorInterface->generateToken();

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            $mailerService->sendEmail(
                $user->getEmail(),
                'Confirmation du compte utilisateur',
                'registration_confirmation.html.twig',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );

            $this->addFlash('success', 'Votre compte a bien été créé, veuillez vérifier vos e-mails pour l\'activer');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/compte/verification/{token}/{id<\d+>}', name: 'app_account_verify', methods: [Request::METHOD_GET])]
    public function accountVerify(
        EntityManagerInterface $entityManager,
        string $token,
        User $user
    ): Response {
        if ($user->getToken() !== $token) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page.');
        }
        if ($user->getToken() === null) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        $user->setVerified(true);
        $user->setToken(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a bien été activé, vous pouvez maintenant vous connectez.');

        return $this->redirectToRoute('app_login');
    }
}
