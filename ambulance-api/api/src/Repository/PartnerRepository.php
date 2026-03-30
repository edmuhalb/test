<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use DomainException;

/**
 * @extends ServiceEntityRepository<Partner>
 *
 * @method Partner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partner[]    findAll()
 * @method Partner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    public function add(Partner $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function save(Partner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Partner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByExternalId(string $externalId): ?Partner
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.externalId = :externalId')
            ->setParameter(':externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getById(int $id): Partner
    {
        $entity = $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$entity) {
            throw new DomainException(\sprintf('Партнер id: %s не найден.', $id));
        }

        return $entity;
    }
}
