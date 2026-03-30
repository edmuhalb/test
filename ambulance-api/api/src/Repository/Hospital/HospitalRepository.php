<?php

declare(strict_types=1);

namespace App\Repository\Hospital;

use App\Entity\Hospital\Hospital;
use App\Entity\Partner;
use DatePeriod;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hospital>
 *
 * @method Hospital|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hospital|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hospital[]    findAll()
 * @method Hospital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hospital::class);
    }

    public function save(Hospital $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Hospital $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByExternal(string $external): ?Hospital
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.external = :external')
            ->setParameter(':external', $external)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByPartnerAndDischargedAt(
        int $partnerId,
        DateTimeImmutable $dischargedAtAfter,
        DateTimeImmutable $dischargedAtBefore
    ) {
        return $this->createQueryBuilder('h')
            ->andWhere('h.partner = :partner')
            ->andWhere('h.dischargedAt >= :dischargedAtAfter')
            ->andWhere('h.dischargedAt < :dischargedAtBefore')
            ->setParameter('partner', $partnerId)
            ->setParameter('dischargedAtAfter', $dischargedAtAfter)
            ->setParameter('dischargedAtBefore', $dischargedAtBefore)
            ->orderBy('h.dischargedAt')
            ->getQuery()
            ->getResult();
    }

    public function findOneByOwnerId(
        int $ownerId,
    ): ?Hospital {
        $data = $this->createQueryBuilder('c')
            ->andWhere('c.owner = :owner')
            ->setParameter(':owner', $ownerId)
            ->getQuery()
            ->getResult();

        $data = $data === null ? [] : $data;

        return array_shift($data);
    }

    public function findByPartnerAndHospitalizedAt(
        int $partnerId,
        DateTimeImmutable $hospitalizedAtAfter,
        DateTimeImmutable $hospitalizedAtBefore
    ) {
        return $this->createQueryBuilder('h')
            ->andWhere('h.partner = :partner')
            ->andWhere('h.hospitalizedAt >= :hospitalizedAtAfter')
            ->andWhere('h.hospitalizedAt < :hospitalizedAtBefore')
            ->andWhere('h.status = :status')
            ->setParameter('partner', $partnerId)
            ->setParameter('hospitalizedAtAfter', $hospitalizedAtAfter)
            ->setParameter('hospitalizedAtBefore', $hospitalizedAtBefore)
            ->setParameter('status', 'inpatient')
            ->orderBy('h.hospitalizedAt')
            ->getQuery()
            ->getResult();
    }

    public function findAllByDischargedAtFromPeriod(DatePeriod $period): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere('c.dischargedAt >= :start')
            ->andWhere('c.dischargedAt < :end')
            ->setParameter('start', $period->getStartDate())
            ->setParameter('end', $period->getEndDate())
            ->getQuery()
            ->getResult();
    }

    public function findAllForPartnerApi(
        Partner $partner,
        ?string $sort,
        ?string $direction,
        ?string $search,
        ?string $statuses,
        ?string $dischargedAtAfter,
        ?string $dischargedAtBefore
    )
    {
        $qb = $this->createQueryBuilder('h');

        if ($search) {
            $qb->where($qb->expr()->like('LOWER(h.fio)', ':search'))
                ->setParameter('search', '%' . $search . '%');
        }

        $qb
            ->andWhere('h.partner = :partner')
            ->setParameter('partner', $partner->getId());

        if ($sort) {
            $qb->orderBy('h.' . $sort, $direction);
        }

        if ($statuses) {
            $statuses = explode(',', $statuses);
            $qb->andWhere($qb->expr()->in('h.status', $statuses));
        }

        if ($dischargedAtAfter && $dischargedAtBefore) {
            $qb->andWhere('h.dischargedAt >= :dischargedAtAfter')
                ->andWhere('h.dischargedAt < :dischargedAtBefore')
                ->setParameter('dischargedAtAfter', $dischargedAtAfter)
                ->setParameter('dischargedAtBefore', $dischargedAtBefore);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findAllCompletedByHospitalizedAtFromPeriod(DatePeriod $period): array
    {
        $qb = $this->createQueryBuilder('h');

        return $qb
            ->andWhere('h.hospitalizedAt >= :start')
            ->andWhere('h.hospitalizedAt < :end')
            ->andWhere($qb->expr()->in('h.status', ['inpatient', 'completed']))
            ->setParameter('start', $period->getStartDate())
            ->setParameter('end', $period->getEndDate())
            ->getQuery()
            ->getResult();
    }
}
