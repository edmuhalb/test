<?php

declare(strict_types=1);

namespace App\Repository\Partner\Agreement;

use App\Entity\Partner\Agreement\AgreementTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AgreementTemplate>
 *
 * @method AgreementTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgreementTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgreementTemplate[]    findAll()
 * @method AgreementTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgreementTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgreementTemplate::class);
    }

    public function save(AgreementTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AgreementTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
