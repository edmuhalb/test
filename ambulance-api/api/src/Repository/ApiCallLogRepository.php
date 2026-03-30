<?php

namespace App\Repository;

use App\Entity\ApiCallLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiCallLog>
 */
class ApiCallLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiCallLog::class);
    }

    public function add(ApiCallLog $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
