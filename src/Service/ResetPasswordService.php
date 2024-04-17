<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\NotFoundException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordService
{
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function sendResetPasswordEmail(string $email): string
    {
        // Logique pour envoyer l'e-mail de réinitialisation de mot de passe
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // Gérer l'exception si la génération du token échoue
            throw new \Exception('Error generating reset token');
        }

        $email = (new TemplatedEmail())
            ->from('rouabhr123@gmail.com')
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context(['resetToken' => $resetToken]);

        $this->mailer->send($email);
        return $resetToken->getToken();
    }

    public function validateTokenAndResetPassword(string $token, string $newPassword): void
    {
        // Logique pour valider le token et réinitialiser le mot de passe
        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            throw new BadRequestHttpException('Invalid token');
        }

        // Hasher le nouveau mot de passe
        $encodedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($encodedPassword);

        // Enregistrer les modifications dans la base de données
        $this->entityManager->flush();
    }
}
