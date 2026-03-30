<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\ServicePayroll;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServicePayroll>
 */
class ServicePayrollRepository extends ServiceEntityRepository
{
    public function __construct(
        private readonly Connection $connection,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, ServicePayroll::class);
    }

    public function removeByCallServiceId(?int $callServiceIdd): void
    {
        $this->createQueryBuilder('s')
            ->delete()
            ->where('s.callService = :callService')
            ->setParameter('callService', $callServiceIdd)
            ->getQuery()
            ->execute();
    }

    public function add(ServicePayroll $entity): void
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

    public function findByRowIds(array $rowIds, int $employeeId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.callService IN (:rowIds)')
            ->andWhere('(c.employee = :employee)')
            ->setParameter('rowIds', $rowIds)
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
            FROM payroll_employee_call_services s
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
