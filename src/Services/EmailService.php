<?php

namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailService
{
    public function __construct(private MailerInterface $mailer, private UrlGeneratorInterface $urlGenerator) {}

    public function sendVerificationEmail(string $receiver, string $verificationToken)
    {

        $verificationUrl = $this->urlGenerator->generate('api_verify_email', [
            'token' => $verificationToken
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new Email())
            ->from('pas-de-reponse@securipass.fr')
            ->to($receiver)
            ->subject('Vérification du compte - SecuriPass')
            ->html(sprintf("<p>Bonjour,</p>
            <p>Cliquez sur le lien ci-dessous pour vérifier votre adresse e-mail et activer votre compte :</p>
            <p><a href='%s'>Vérifier mon e-mail</a></p>
            <span>Ce lien expirera dans 15 minutes.</span>
            <p>Si vous n'avez pas demandé cette vérification, ignorez cet e-mail.</p>", $verificationUrl));

        $this->mailer->send($email);
    }
}
