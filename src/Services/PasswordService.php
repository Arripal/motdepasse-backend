<?php

namespace App\Services;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class PasswordService
{

    private $passwordHasher;

    public function __construct()
    {
        $this->passwordHasher = new PasswordHasherFactory(['default' => ['algorithm' => 'bcrypt']]);
    }

    public function hashPassword(string $password)
    {
        $passwordHasher = $this->passwordHasher->getPasswordHasher('default');
        return $passwordHasher->hash($password);
    }
}
