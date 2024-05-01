<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    #[Route('/test-email', name: 'test_email', methods: [Request::METHOD_GET])]
    public function testEmail(MailerInterface $mailer): Response
    {
        $senderEmail = $this->getParameter('app.senderEmail');

        $email = (new Email())
            ->from(new Address($senderEmail, 'Nicolas Castagna'))
            ->to('example@example.com')
            ->subject('Test email')
            ->text('This is a test email sent from Symfony Mailer.');

        $mailer->send($email);

        return new Response('Test email sent!');
    }
}
