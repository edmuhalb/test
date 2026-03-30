<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\CallPayroll;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CallPayroll>
 */
class CallPayrollRepository extends ServiceEntityRepository
{
    public function __construct(
        private readonly Connection $connection,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, CallPayroll::class);
    }

    public function removeByCallServiceId(?int $callId): void
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->where('c.call = :call')
            ->setParameter('call', $callId)
            ->getQuery()
            ->execute();
    }

    public function add(CallPayroll $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function findByPlannedEmployee(DateTimeImmutable $accruedAfter, DateTimeImmutable $accruedBefore, int $employeeId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.accruedAt >= :accruedAtAfter')
            ->andWhere('c.accruedAt < :accruedAtBefore')
            ->andWhere('(c.employee = :employee)')
            ->setParameter('accruedAtAfter', $accruedAfter)
            ->setParameter('accruedAtBefore', $accruedBefore)
            ->setParameter('employee', $employeeId)
            ->orderBy('c.accruedAt')
            ->getQuery()
            ->getResult();
    }

    public function findByCallIds(array $callIds, int $employeeId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.call IN (:callIds)')
            ->andWhere('(c.employee = :employee)')
            ->setParameter('callIds', $callIds)
            ->setParameter('employee', $employeeId)
            ->getQuery()
            ->getResult();
    }

    public function findByAccruedAt(DateTimeImmutable $accruedAfter, DateTimeImmutable $accruedBefore)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.accruedAt >= :accruedAtAfter')
            ->andWhere('c.accruedAt < :accruedAtBefore')
            ->setParameter('accruedAtAfter', $accruedAfter)
            ->setParameter('accruedAtBefore', $accruedBefore)
            ->orderBy('c.accruedAt')
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
        $sql = 'SELECT SUM(c.accrued_amount) AS amount
            FROM payroll_employee_calls c
            WHERE c.accrued_at BETWEEN :start_date AND :end_date
            AND c.employee_id = :employee_id';

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
