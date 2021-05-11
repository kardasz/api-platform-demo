<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserVoter.
 */
class UserVoter extends Voter
{
    private const VIEW = 'view';
    private const DELETE = 'delete';
    private const UPDATE = 'update';
    private const NOTIFY = 'notify';
    private const DEACTIVATE = 'deactivate';
    private const ACTIVATE = 'activate';
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
                self::VIEW,
                self::DELETE,
                self::UPDATE,
                self::NOTIFY,
                self::DEACTIVATE,
                self::ACTIVATE,
            ]
        ) && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::UPDATE:
                return $this->security->isGranted('ROLE_ADMIN')
                    || $this->canUpdate($subject, $user);
                break;
            case self::DELETE:
                return $this->canDelete($subject, $user)
                    || $this->security->isGranted('ROLE_ADMIN');
                break;
            case self::ACTIVATE:
                return $this->canActivate($subject, $user);
                break;
            case self::DEACTIVATE:
                return $this->canDeactivate($subject, $user);
                break;
            case self::NOTIFY:
                return $this->canNotify($subject, $user)
                    || $this->security->isGranted('ROLE_ADMIN');
                break;
        }

        return false;
    }

    private function canDelete(User $subject, User $user): bool
    {
        return $subject !== $user && $this->isManagerOf($user, $subject);
    }

    private function canUpdate(User $subject, User $user): bool
    {
        return $subject === $user || $this->isManagerOf($user, $subject);
    }

    private function canActivate(User $subject, User $user): bool
    {
        return !$subject->isActive() &&
            (
                $this->isManagerOf($user, $subject)
                || $this->security->isGranted('ROLE_ADMIN')
            );
    }

    private function canDeactivate(User $subject, User $user): bool
    {
        return $subject->isActive() &&
            (
                $this->isManagerOf($user, $subject)
                || $this->security->isGranted('ROLE_ADMIN')
            );
    }

    private function canNotify(User $subject, User $user): bool
    {
        return $this->isManagerOf($user, $subject);
    }

    private function isManagerOf(User $user, User $subject): bool
    {
        return $subject->getManager() === $user;
    }
}
