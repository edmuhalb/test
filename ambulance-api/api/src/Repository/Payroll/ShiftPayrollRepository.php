<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\ShiftPayroll;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShiftPayroll>
 */
class ShiftPayrollRepository extends ServiceEntityRepository
{
    public function __construct(
        private readonly Connection $connection,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, ShiftPayroll::class);
    }

    public function removeByShiftId(?int $shiftId): void
    {
        $this->createQueryBuilder('sh')
            ->delete()
            ->where('sh.shift = :shift')
            ->setParameter('shift', $shiftId)
            ->getQuery()
            ->execute();
    }

    public function add(ShiftPayroll $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function findByPlannedEmployee(DateTimeImmutable $accruedAfter, DateTimeImmutable $accruedBefore, int $employeeId)
    {
        return $this->createQueryBuilder('sh')
            ->andWhere('sh.accruedAt >= :accruedAtAfter')
            ->andWhere('sh.accruedAt < :accruedAtBefore')
            ->andWhere('(sh.employee = :employee)')
            ->setParameter('accruedAtAfter', $accruedAfter)
            ->setParameter('accruedAtBefore', $accruedBefore)
            ->setParameter('employee', $employeeId)
            ->orderBy('sh.accruedAt')
            ->getQuery()
            ->getResult();
    }

    public function findByShiftIds(array $shiftIds, int $employeeId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.shift IN (:shiftIds)')
            ->andWhere('(s.employee = :employee)')
            ->setParameter('shiftIds', $shiftIds)
            ->setParameter('employee', $employeeId)
            ->getQuery()
            ->getResult();
    }

    public function findByAccruedAt(DateTimeImmutable $accruedAfter, DateTimeImmutable $accruedBefore)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.accruedAt >= :accruedAtAfter')
            ->andWhere('s.accruedAt < :accruedAtBefore')
            ->setParameter('accruedAtAfter', $accruedAfter)
            ->setParameter('accruedAtBefore', $accruedBefore)
            ->orderBy('s.accruedAt')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws Exception
     */
    public function findAccruedSumByAccruedAt(
        DateTimeImmutable $accruedAfter,
        DateTimeImmutable $accruedBefore,
        int $employeeId
    ): int {
        $sql = 'SELECT SUM(s.accrued_amount) AS amount
            FROM payroll_employee_shifts s
            WHERE s.accrued_at BETWEEN :start_date AND :end_date
            AND s.employee_id = :employee_id';

        $startDate = $accruedAfter->format('Y-m-d H:i:s');
        $endDate = $accruedBefore->format('Y-m-d H:i:s');

        $statement = $this->connection->executeQuery($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'employee_id' => $employeeId,
        ]);

        $result = $statement->fetchAllAssociative();
        return $result[0]['amount'] ? (int)$result[0]['amount'] : 0;
    }
}
