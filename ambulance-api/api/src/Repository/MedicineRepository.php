<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Medicine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Medicine>
 *
 * @method Medicine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Medicine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Medicine[]    findAll()
 * @method Medicine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicine::class);
    }

    public function save(Medicine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Medicine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
