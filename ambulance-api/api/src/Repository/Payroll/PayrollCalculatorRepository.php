<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\PayrollCalculator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PayrollCalculator>
 */
class PayrollCalculatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayrollCalculator::class);
    }

    public function add(PayrollCalculator $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function findByTarget(string $target)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.target = :target')
            ->setParameter('target', $target)
            ->getQuery()
            ->getResult();
    }

    public function findOneByProcessor(string $processor):?PayrollCalculator
    {
        $calculators = $this->createQueryBuilder('d')
            ->andWhere('d.processor = :processor')
            ->setParameter('processor', $processor)
            ->getQuery()
            ->getResult();

        return array_shift($calculators);
    }
}
