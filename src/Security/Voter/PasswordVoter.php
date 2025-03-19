<?php

namespace App\Security\Voter;

use App\Entity\Password;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class PasswordVoter extends Voter
{
    public const EDIT = 'PASSWORD_EDIT';
    public const DELETE = 'PASSWORD_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Password;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof Password) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $subject->getOwner()->getEmail() === $user->getEmail();
                break;

            case self::DELETE:
                return $subject->getOwner()->getEmail() === $user->getEmail();
                break;
        }

        return false;
    }
}
