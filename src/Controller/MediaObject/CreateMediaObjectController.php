<?php

namespace App\Controller\MediaObject;

use App\Entity\MediaObject;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;

/**
 * Class CreateMediaObjectController.
 *
 * Single action controller using the __invoke() method,
 * which is a common practice when following the ADR pattern (Action-Domain-Responder)
 *
 * @see https://symfony.com/doc/current/controller/service.html#invokable-controllers
 * @see https://api-platform.com/docs/core/controllers/
 */
class CreateMediaObjectController
{
    private EntityManagerInterface $em;
    private Security $security;

    /**
     * CreateMediaObjectController constructor.
     */
    public function __construct(
        EntityManagerInterface $em,
        Security $security
    ) {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(Request $request): MediaObject
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $mediaObject = new MediaObject();
        $mediaObject->setFile($uploadedFile);
        $mediaObject->setOwner($this->security->getUser());
        $mediaObject->setCreatedAt(new DateTime());

        return $mediaObject;
    }
}
