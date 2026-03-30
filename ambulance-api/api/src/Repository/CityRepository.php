<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DomainException;

/**
 * @extends ServiceEntityRepository<City>
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function findOneByExternalId(string $externalId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.externalId = :externalId')
            ->setParameter(':externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getById(int $id): City
    {
        $entity = $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$entity) {
            throw new DomainException(\sprintf('Город id: %s не найден.', $id));
        }

        return $entity;
    }
}
