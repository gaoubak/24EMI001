<?php

namespace App\Service;

use App\DTO\FormDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private MailerService $mailerService,
        private EntityManagerInterface $entityManager
    )
    {

    }

    public function createPasswordCreationToken(User $user): void
    {
        //$user->setPassword(null);

        $passwordToken = md5(bin2hex(random_bytes(10)));

        $user->setPasswordCreationToken($passwordToken);
        $user->setPasswordCreationDateExpiration((new \DateTime())->modify('+1 month'));

        $this->mailerService->sendPasswordForgottenMail($user);
    }

    public function setPassword(User $user, string $password): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);

        $user->setPassword($hashedPassword);
        $user->setPasswordCreationToken(null);
        $user->setPasswordCreationDateExpiration(null);
    }  
}