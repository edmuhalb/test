<?php

declare(strict_types=1);

namespace App\Repository\PaymentSetting;

use App\Entity\PaymentSetting\PaymentSettingVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentSettingVersion>
 *
 * @method PaymentSettingVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentSettingVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentSettingVersion[]    findAll()
 * @method PaymentSettingVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentSettingVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentSettingVersion::class);
    }

    public function save(PaymentSettingVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PaymentSettingVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return PaymentSettingVersion[] Returns an array of PaymentSettingVersion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PaymentSettingVersion
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
