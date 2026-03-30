<?php

declare(strict_types=1);

namespace App\Query\PartnerReward;

use App\Entity\Partner\Agreement\Agreement;
use App\Repository\Partner\Agreement\AgreementRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class Fetcher
{
    public function __construct(
        private readonly Connection $connection,
        private readonly AgreementRepository $agreements,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(Query $query): int
    {
        $agreement = $this->agreements->findCurrentByPartnerId(
            $query->partnerId,
            $query->time
        );

        if (!$agreement) {
            return 0;
        }

        $distance = null;

        if ($query->distance === 0) {
            $distance = $this->getEqualDistance($agreement, $query);
        }

        if ($distance === null) {
            $distance = $this->getMaxDistance($agreement, $query);
        }
        if ($distance === null) {
            $distance = $this->getMinDistance($agreement, $query);
        }

        return $this->getPercent($agreement, $distance, $query);
    }

    /**
     * @throws Exception
     */
    private function getEqualDistance(Agreement $agreement, Query $query): ?int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('r.distance as value')
            ->from('`row`', 'r')
            ->andWhere('r.agreement_id = :agreementId')
            ->setParameter('agreementId', $agreement->getId())
            ->andWhere('r.service_id = :service')
            ->setParameter('service', $query->serviceId)
            ->andWhere('r.distance = :distance')
            ->setParameter('distance', $query->distance);

        $qb->orderBy('r.distance', 'ASC');
        $stmt = $qb->executeQuery();
        $row = $stmt->fetchAssociative() ?: [];
        return array_shift($row);
    }

    /**
     * @throws Exception
     */
    private function getMaxDistance(Agreement $agreement, Query $query): ?int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('r.distance as value')
            ->from('`row`', 'r')
            ->andWhere('r.agreement_id = :agreementId')
            ->setParameter('agreementId', $agreement->getId())
            ->andWhere('r.service_id = :service')
            ->setParameter('service', $query->serviceId)
            ->andWhere('r.distance > :distance')
            ->setParameter('distance', $query->distance);

        $qb->orderBy('r.distance', 'ASC');
        $stmt = $qb->executeQuery();
        $row = $stmt->fetchAssociative() ?: [];
        return array_shift($row);
    }

    /**
     * @throws Exception
     */
    private function getMinDistance(Agreement $agreement, Query $query): ?int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('r.distance as value')
            ->from('`row`', 'r')
            ->andWhere('r.agreement_id = :agreementId')
            ->setParameter('agreementId', $agreement->getId())
            ->andWhere('r.service_id = :service')
            ->setParameter('service', $query->serviceId)
            ->andWhere('r.distance <= :distance')
            ->setParameter('distance', $query->distance);

        $qb->orderBy('r.distance', 'DESC');
        $stmt = $qb->executeQuery();
        $row = $stmt->fetchAssociative() ?: [];
        return array_shift($row);
    }

    /**
     * @param mixed $distance
     * @throws Exception
     */
    private function getPercent(Agreement $agreement, $distance, Query $query): int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('r.percent as value')
            ->from('`row`', 'r')
            ->leftJoin('r', 'agreement', 'a', 'a.id = r.agreement_id')
            ->andWhere('a.id = :agreementId')
            ->setParameter('agreementId', $agreement->getId())
            ->andWhere('r.service_id = :service')
            ->setParameter('service', $query->serviceId)
            ->andWhere('r.repeat_number <= :repeat')
            ->setParameter('repeat', $query->repeat)
            ->andWhere('r.distance = :distance')
            ->setParameter('distance', $distance);

        $qb->orderBy('r.repeat_number', 'DESC');
        $stmt = $qb->executeQuery();

        $row = $stmt->fetchAssociative() ?: [];

        $result = array_shift($row);

        return $result === null ? 0 : (int)$result;
    }
}
