<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private PaginationExtension $pagination;
    private Security $security;
    private UserRepository $repository;

    public function __construct(
        PaginationExtension $pagination,
        Security $security,
        UserRepository $repository
    ) {
        $this->pagination = $pagination;
        $this->security = $security;
        $this->repository = $repository;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $qb = $this->repository->createCollectionQueryBuilder(
            (!$this->security->isGranted('ROLE_ADMIN')) ? $this->security->getUser() : null
        );

        $this->pagination->applyToCollection($qb, new QueryNameGenerator(), $resourceClass, $operationName, $context);

        return $this->pagination->getResult($qb, $resourceClass, $operationName, $context);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass;
    }
}
