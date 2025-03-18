<?php

namespace App\Services;

use Ramsey\Uuid\Guid\Guid;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class RegistrationService
{
    public function __construct(private CacheInterface $cacheInterface) {}

    public function createRegistrationToken(string $email)
    {
        $verificationToken = Guid::uuid4()->toString();

        $cacheKey = 'pending_user_token_' . $verificationToken;
        $this->cacheInterface->delete($cacheKey);
        $this->cacheInterface->get($cacheKey, function (ItemInterface $itemInterface) use ($verificationToken, $email) {
            $itemInterface->expiresAfter(900); // 15 minutes
            return ["token" => $verificationToken, "email" => $email];
        });
        return $verificationToken;
    }

    public function getEmailFromToken(string $token)
    {
        $cacheKey = 'pending_user_token_' . $token;
        $cachedEmail = $this->cacheInterface->get($cacheKey, function (ItemInterface $itemInterface) {
            $itemInterface->expiresAfter(0);
            return null;
        });

        if (!$cachedEmail) {
            return null;
        }

        return $cachedEmail['email'];
    }
}
