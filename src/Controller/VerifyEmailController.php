<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Services\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VerifyEmailController extends AbstractController
{
    #[Route('/api/verify-email/{token}', name: 'api_verify_email')]
    public function VerifyEmail(UserRepository $userRepository, EntityManagerInterface $entityManager, $token, RegistrationService $registrationService): Response
    {

        $email = $registrationService->getEmailFromToken($token);

        if (!$email) {
            return $this->json(['message' => "Impossible de procéder à la vérification, le lien a expiré."], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json(["message" => "Aucun utilisateur correspondant à l'adresse mail reçue."], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user->setStatus('verified');

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => "Votre inscription a bien été prise en compte. Vous pouvez maintenant vous connecter à votre compte."], 201);
    }
}
