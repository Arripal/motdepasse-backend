<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\EmailService;
use App\Services\RegistrationService as ServicesRegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;


final class RegistrationController extends AbstractController
{

    public function __construct(private CacheInterface $cacheInterface, private ServicesRegistrationService $registrationService) {}

    #[Route('/api/registration', name: 'app_registration', methods: ['POST'])]
    public function registration(Request $request, UserPasswordHasherInterface $userPasswordHasher, ValidatorInterface $validator, EntityManagerInterface $entityManager, EmailService $emailService): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || !isset($data['email']) || !isset($data['password'])) {
            return $this->json(['message' => 'Vous devez fournir des identifiants valides.'], 400);
        }

        $user = new User();

        $user->setEmail($data['email']);
        $user->setPassword($data['password']);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {

            return $this->json($errors, 403);
        }

        $hashedPassword = $userPasswordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $user->setStatus('pending');

        $entityManager->persist($user);
        $entityManager->flush();

        $registrationToken = $this->registrationService->createRegistrationToken($user->getEmail());
        $emailService->sendVerificationEmail($user->getEmail(), $registrationToken);

        return $this->json([
            'message' => "Un email de vérification a été envoyé à l'adresse fournie. Veuillez suivre les instructions. Le lien expirera après 15 minutes.",
            "registration_token" => $registrationToken
        ], 201);
    }
}
