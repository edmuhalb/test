<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AdministratorReport;
use App\Entity\MedTeam\MedTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdministratorReport>
 *
 * @method AdministratorReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdministratorReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdministratorReport[]    findAll()
 * @method AdministratorReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdministratorReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdministratorReport::class);
    }

    public function save(AdministratorReport $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AdministratorReport $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByMedTeam(MedTeam $medTeam): ?AdministratorReport
    {
        $qb = $this->createQueryBuilder('ar');

        $teams = $qb
            ->andWhere('ar.team = :team')
            ->setParameter(':team', $medTeam->getId())
            ->orderBy('ar.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return array_shift($teams);
    }
}
