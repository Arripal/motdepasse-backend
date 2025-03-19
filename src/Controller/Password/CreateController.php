<?php

namespace App\Controller\Password;

use App\Entity\Site;
use App\Entity\Password;
use App\Services\PasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateController extends AbstractController
{

    public function __construct(private PasswordService $passwordService, private EntityManagerInterface $entityManager) {}

    #[Route('/api/password/create', name: 'api_password_create')]
    public function create(Request $request, ValidatorInterface $validator): Response
    {

        $data = json_decode($request->getContent(), true);

        $this->verifyData($data);

        $password = new Password();
        $site = $this->entityManager->getReference(Site::class, $data['siteId']);

        $password->setOwner($this->getUser());
        $password->setSite($site);
        $password->setPassword($data['password']);

        $errors = $validator->validate($password);

        if (count($errors) > 0) {

            return $this->json($errors, 403);
        }

        $hashedPassword = $this->passwordService->hashPassword($password->getPassword());
        $password->setPassword($hashedPassword);

        $this->entityManager->persist($password);
        $this->entityManager->flush();

        return $this->json(["message" => "Le mot de passe a bien été enregistré."], 201);
    }

    private function verifyData($dataObject)
    {
        if (!is_array($dataObject) || !isset($dataObject['password']) || !isset($dataObject['siteId'])) {
            return $this->json(['message' => 'Il manque des données.Veuillez réessayer.'], 400);
        }
    }
}
