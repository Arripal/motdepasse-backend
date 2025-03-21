<?php

namespace App\Controller\Password;

use App\Entity\Site;
use App\Entity\Password;
use App\Repository\SiteRepository;
use App\Services\PasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateController extends AbstractController
{

    public function __construct(private PasswordService $passwordService, private EntityManagerInterface $entityManager, private SiteRepository $siteRepository) {}

    #[Route('/api/password/create', name: 'api_password_create')]
    public function create(Request $request, ValidatorInterface $validator): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$this->issetData($data)) {
            return $this->json(['message' => 'Il manque des données.Veuillez réessayer.'], 400);
        }

        $siteData = $data['site'];
        $passwordData = $data['password'];

        $existingSite = $this->isExistingSite($siteData['name']);
        if ($existingSite) {
            return $this->json(['error' => "Un mot de passe est déjà associé à ce site."], 409);
        }

        $site = new Site();
        $site->setName($siteData['name'])->setUrl($siteData['url'] ?? null);
        $password = new Password();
        $password->setOwner($this->getUser())->setSite($site)->setPassword($passwordData['value']);

        $jsonReponse = $this->validateData($password, $validator);

        if ($jsonReponse) {
            return $jsonReponse;
        }

        $jsonReponse = $this->validateData($site, $validator);

        if ($jsonReponse) {
            return $jsonReponse;
        }

        $hashedPassword = $this->passwordService->hashPassword($password->getPassword());
        $password->setPassword($hashedPassword);

        $this->entityManager->persist($site);
        $this->entityManager->persist($password);
        $this->entityManager->flush();

        return $this->json(["message" => "Le mot de passe a bien été enregistré."], 201);
    }

    private function issetData($dataObject)
    {
        return is_array($dataObject) && isset($dataObject['password']) && isset($dataObject['site']);
    }

    private function validateData($entity, ValidatorInterface $validator)
    {
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            return new JsonResponse((string) $errors, 403);
        }
        return null;
    }

    private function isExistingSite(string $site)
    {
        $qb = $this->siteRepository->createQueryBuilder('s')
            ->where('LOWER(s.name) = LOWER(:name)')
            ->setParameter('name', $site);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
