<?php

declare(strict_types=1);

namespace App\Repository\Payroll\KpiDocument;

use App\Entity\Payroll\KpiDocument\KpiDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<KpiDocument>
 */
class KpiDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KpiDocument::class);
    }

    public function add(KpiDocument $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove(KpiDocument $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function findById($id): ?KpiDocument
    {
        return $this->find($id);
    }

    public function getById($id): KpiDocument
    {
        $result = $this->find($id);

        if (!$result) {
            throw new NotFoundHttpException('KPI документ #' . $id . ' не найден');
        }
        return $result;
    }
}
