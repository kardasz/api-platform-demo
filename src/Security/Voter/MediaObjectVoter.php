<?php

namespace App\Security\Voter;

use App\Entity\MediaObject;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Class MediaObjectVoter.
 */
class MediaObjectVoter extends Voter
{
    private const VIEW = 'view';
    private const DELETE = 'delete';
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array(
            $attribute,
            [
                self::DELETE,
                self::VIEW,
            ]
        ) && $subject instanceof MediaObject;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // admin can do anything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::DELETE:
                return $this->isOwnerOf($user, $subject)
                    || $this->isManagerOf($user, $subject);
                break;
        }

        return false;
    }

    private function isOwnerOf(User $user, MediaObject $subject): bool
    {
        return $subject->getOwner() === $user;
    }

    private function isManagerOf(User $user, MediaObject $subject): bool
    {
        return $subject->getOwner()->getManager() === $user;
    }
}
