<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private string $webappUrl,
        private MailerInterface $mailerInterface
    )
    {

    }

    public function sendPasswordForgottenMail(User $user): void
    {
      $url = $this->webappUrl . '/user/login/password-define/' . $user->getPasswordCreationToken();

      $this->sendMail(
        to: $user->getEmail(),
        subject: 'Mot de passe oublié',
        message: 'Cliquez sur ce lien pour réinitialiser votre mot de passe: ' . $url
      );
    }

    public function sendPasswordSetupEmail(User $user): void
    {
      $url = $this->webappUrl . '/user/login/password-setup/' . $user->getPasswordCreationToken();

      $this->sendMail(
        to: $user->getEmail(),
        subject: 'Creation de Compte',
        message: 'Cliquez sur ce lien pour crée votre mot de passe: ' . $url
      );
    }

    private function sendMail(
        string $to,
        string $subject,
        string $message
    ): void
    {
        $email = (new Email())
            ->from('noreply@sicko.fr')
            ->to($to)
            ->subject($subject)
            ->html($message);

        $this->mailerInterface->send($email);
    }
}
