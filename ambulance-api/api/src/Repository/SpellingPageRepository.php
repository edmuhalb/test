<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SpellingPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpellingPage>
 *
 * @method SpellingPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpellingPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpellingPage[]    findAll()
 * @method SpellingPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpellingPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpellingPage::class);
    }

    public function save(SpellingPage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SpellingPage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByIsChecked($isChecked, $count = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.isChecked = :val')
            ->setParameter('val', $isChecked);

        if ($count) {
            $qb->setMaxResults($count);
        }

        return $qb->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return SpellingPage[] Returns an array of SpellingPage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SpellingPage
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
