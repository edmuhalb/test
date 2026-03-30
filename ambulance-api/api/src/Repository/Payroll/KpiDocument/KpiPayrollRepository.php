<?php

declare(strict_types=1);

namespace App\Repository\Payroll\KpiDocument;

use App\Entity\Payroll\KpiDocument\KpiPayroll;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<KpiPayroll>
 */
class KpiPayrollRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, KpiPayroll::class);
    }

    public function findByPlannedEmployee(DateTimeImmutable $accruedAfter, DateTimeImmutable $accruedBefore, int $employeeId)
    {
        return $this->createQueryBuilder('kp')
            ->leftJoin('kp.record', 'r')
            ->andWhere('kp.accruedAt > :accruedAtAfter')
            ->andWhere('kp.accruedAt <= :accruedAtBefore')
            ->andWhere('(r.employee = :employee)')
           ->setParameter('accruedAtAfter', $accruedAfter)
            ->setParameter('accruedAtBefore', $accruedBefore)
            ->setParameter('employee', $employeeId)
            ->orderBy('kp.accruedAt')
            ->getQuery()
            ->getResult();
    }

    public function findByAccruedAt(DateTimeImmutable $accruedAfter, DateTimeImmutable $accruedBefore)
    {
        return $this->createQueryBuilder('kp')
             ->andWhere('kp.accruedAt > :accruedAtAfter')
             ->andWhere('kp.accruedAt <= :accruedAtBefore')
             ->setParameter('accruedAtAfter', $accruedAfter)
             ->setParameter('accruedAtBefore', $accruedBefore)
            ->orderBy('kp.accruedAt')
            ->getQuery()
            ->getResult();
    }
}
