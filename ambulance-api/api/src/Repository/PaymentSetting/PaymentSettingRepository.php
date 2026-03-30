<?php

declare(strict_types=1);

namespace App\Repository\PaymentSetting;

use App\Entity\PaymentSetting\PaymentSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * @extends ServiceEntityRepository<PaymentSetting>
 *
 * @method PaymentSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentSetting[]    findAll()
 * @method PaymentSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentSetting::class);
    }

    public function save(PaymentSetting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PaymentSetting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOperatorPercentCoding(): int
    {
        return $this->get(PaymentSetting::OPERATOR_PERCENT_CODING)->getValue();
    }

    public function getOperatorPercentHospital(): int
    {
        return $this->get(PaymentSetting::OPERATOR_PERCENT_HOSPITAL)->getValue();
    }

    public function getOperatorPercentTherapy(): int
    {
        return $this->get(PaymentSetting::OPERATOR_PERCENT_THERAPY)->getValue();
    }

    public function getOperatorRewardStationary(): int
    {
        return $this->get(PaymentSetting::OPERATOR_REWARD_STATIONARY)->getValue();
    }

    public function get($value): PaymentSetting
    {
        $result = $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$result) {
            throw new NotFoundResourceException();
        }

        return $result;
    }
}
