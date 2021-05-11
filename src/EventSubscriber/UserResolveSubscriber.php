<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;

/**
 * Class UserResolveSubscriber.
 */
class UserResolveSubscriber implements EventSubscriberInterface
{
    private UserProviderInterface $userProvider;

    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(
        UserProviderInterface $userProvider,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->userProvider = $userProvider;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function onUserResolve(UserResolveEvent $event): void
    {
        try {
            $user = $this->userProvider->loadUserByUsername($event->getUsername());
        } catch (UsernameNotFoundException $e) {
            return;
        }

        if (!$this->userPasswordEncoder->isPasswordValid($user, $event->getPassword())) {
            return;
        }

        $event->setUser($user);
    }

    public static function getSubscribedEvents()
    {
        return [
            'trikoder.oauth2.user_resolve' => 'onUserResolve',
        ];
    }
}
