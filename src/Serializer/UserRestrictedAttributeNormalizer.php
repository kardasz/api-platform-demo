<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

/**
 * Class UserAttributeNormalizer.
 *
 * Adding serialization group "user:restricted" if current user is admin
 * or if it's manager of serialized object.
 */
class UserRestrictedAttributeNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public const GROUP = 'user:restricted';
    private const ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if ($this->userHasPermissionsForUser($object)) {
            $context['groups'][] = self::GROUP;
        }

        $context[self::ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $this->security->getUser() instanceof User
            && $data instanceof User;
    }

    private function userHasPermissionsForUser(User $object): bool
    {
        return $this->security->isGranted('ROLE_ADMIN') // admin can read anything
            || $this->isManagerOf($object) // manager can read managed user
            || $object === $this->security->getUser(); // user can read self
    }

    private function isManagerOf(User $object): bool
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->security->isGranted('ROLE_MANAGER') && $object->getManager() === $user;
    }
}
