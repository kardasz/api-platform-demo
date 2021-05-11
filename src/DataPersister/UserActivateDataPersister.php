<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class UserActivateDataPersister.
 */
class UserActivateDataPersister implements ContextAwareDataPersisterInterface
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;

    /**
     * UserSelfRegistrationDataPersister constructor.
     */
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * @param User $data
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User
            && isset($context['collection_operation_name'])
            && in_array($context['collection_operation_name'], ['user_activate', 'user_deactivate']);
    }

    /**
     * @param User $data
     *
     * @return User
     */
    public function persist($data, array $context = [])
    {
        // Some custom logic...
        $data->setActive('user_activate' === $context['collection_operation_name']);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }

    public function remove($data, array $context = [])
    {
        throw new BadRequestHttpException();
    }
}
