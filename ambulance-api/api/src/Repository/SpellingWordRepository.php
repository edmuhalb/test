<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SpellingWord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpellingWord>
 *
 * @method SpellingWord|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpellingWord|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpellingWord[]    findAll()
 * @method SpellingWord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpellingWordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpellingWord::class);
    }

    public function save(SpellingWord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SpellingWord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByWord($value): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.word = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}
