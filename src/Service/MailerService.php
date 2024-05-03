<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService extends AbstractController
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * sendEmail
     *
     * @param  string $recipientEmail
     * @param  string $subject
     * @param  string $twig
     * @param  array $context
     * @throws TransportExceptionInterface
     * 
     * @return void
     */
    public function sendEmail(string $recipientEmail, string $subject, string $twig, array $context): void
    {
        $senderEmail = $this->getParameter('app.senderEmail');

        $email = (new TemplatedEmail())
            ->from(new Address($senderEmail, 'Nicolas Castagna'))
            ->to($recipientEmail)
            ->subject($subject)
            ->htmlTemplate("mails/$twig")
            ->context($context);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $transportException) {
            throw $transportException;
        }
    }
}
