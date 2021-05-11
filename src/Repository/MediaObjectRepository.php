<?php

namespace App\Repository;

use App\Entity\MediaObject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MediaObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaObject[]    findAll()
 * @method MediaObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaObject::class);
    }

    public function createCollectionQueryBuilder(?User $user = null): QueryBuilder
    {
        $query = $this->createQueryBuilder('m');

        if (null !== $user) {
            $query
                ->join('m.owner', 'owner')
                ->andWhere('(m.owner = :user OR owner.manager = :user)')
                ->setParameter('user', $user);
        }

        return $query;
    }
}
