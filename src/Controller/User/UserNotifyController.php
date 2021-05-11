<?php

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserNotifyController.
 *
 * Single action controller using the __invoke() method,
 * which is a common practice when following the ADR pattern (Action-Domain-Responder)
 *
 * @see https://symfony.com/doc/current/controller/service.html#invokable-controllers
 * @see https://api-platform.com/docs/core/controllers/
 */
class UserNotifyController
{
    private EntityManagerInterface $em;

    /**
     * UserNotifyController constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(
        Request $request,
        User $user
    ): void {
        // Custom logic ...
        // read more https://api-platform.com/docs/core/controllers/
        // read more https://symfony.com/doc/current/controller/service.html#invokable-controllers
    }
}
