<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserSelfRegistrationDataPersister.
 */
class UserSelfRegistrationDataPersister implements ContextAwareDataPersisterInterface
{
    /** @var UserPasswordEncoderInterface */
    private UserPasswordEncoderInterface $passwordEncoder;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;

    /**
     * UserSelfRegistrationDataPersister constructor.
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }

    /**
     * @param User $data
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User
            && isset($context['collection_operation_name'])
            && 'self_registration' === $context['collection_operation_name'];
    }

    /**
     * @param User $data
     *
     * @return User
     */
    public function persist($data, array $context = [])
    {
        $data->setPassword(
            $this->passwordEncoder->encodePassword(
                $data, $data->getPlainPassword()
            )
        );

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }

    public function remove($data, array $context = [])
    {
        throw new BadRequestHttpException();
    }
}
