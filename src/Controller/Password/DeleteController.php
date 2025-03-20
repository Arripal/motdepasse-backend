<?php

namespace App\Controller\Password;

use App\Repository\PasswordRepository;
use App\Security\Voter\PasswordVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteController extends AbstractController
{
    #[Route('/api/password/delete', name: 'api_password_delete', methods: ['DELETE'])]
    public function delete(Request $request, PasswordRepository $passwordRepository, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['passwordId'])) {
            return $this->json(['error' => "Impossible de supprimer votre mot de passe."], 400);
        }

        $password = $passwordRepository->find($data['passwordId']);

        if (!$password) {
            return $this->json(['error' => 'Impossible de supprimer votre mot de passe.', 404]);
        }

        $this->denyAccessUnlessGranted(PasswordVoter::DELETE, $password);

        $entityManager->remove($password);
        $entityManager->flush();

        return $this->json(['message' => "Le mot de passe a bien été supprimé."], 201);
    }
}
