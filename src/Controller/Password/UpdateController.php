<?php

namespace App\Controller\Password;

use App\Repository\PasswordRepository;
use App\Security\Voter\PasswordVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UpdateController extends AbstractController
{
    #[Route('/api/password/update', name: 'api_password_update')]
    #[IsGranted(PasswordVoter::EDIT)]
    public function update(Request $request, EntityManagerInterface $entityManager, PasswordRepository $passwordRepository): Response
    {
        return $this->json(["success" => "Le mot de passe a bien été modifié."]);
    }
}
