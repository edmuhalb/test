<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\Partner;
use App\Entity\Team\Team;
use App\Entity\User\User;
use DatePeriod;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Calling>
 *
 * @method Calling|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calling|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calling[]    findAll()
 * @method Calling[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calling::class);
    }

    public function add(Calling $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function save(Calling $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Calling $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getById($id): Calling
    {
        $result = $this->find($id);

        if (!$result) {
            throw new NotFoundHttpException('Вызов id: ' . $id . ' не найден');
        }
        return $result;
    }

    public function findActiveByAdministrator(User $user): ?Calling
    {
        $statuses = [
            Status::ACCEPTED,
            Status::ARRIVED,
        ];

        $qb = $this->createQueryBuilder('c');

        $teams = $qb
            ->andWhere('c.admin = :admin')
            ->setParameter(':admin', $user->getId())
            ->andWhere($qb->expr()->in('c.status', $statuses))
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return array_shift($teams);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getCurrentByTeam(Team $team): Calling
    {
        $statuses = [
            Status::ACCEPTED,
            Status::ASSIGNED,
            Status::ARRIVED,
        ];

        $qb = $this->createQueryBuilder('c');

        if (!$checkWordstat = $qb
            ->andWhere('c.team = :team')
            ->setParameter(':team', $team->getId())
            ->andWhere($qb->expr()->in('c.status', $statuses))
            ->getQuery()
            ->getOneOrNullResult()
        ) {
            throw new NotFoundHttpException('Нет текущего заказа');
        }
        return $checkWordstat;
    }

    public function findOneByNumber(string $numberCalling): ?Calling
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.numberCalling = :numberCalling')
            ->setParameter(':numberCalling', $numberCalling)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getOneByNumber(string $numberCalling): Calling
    {
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.numberCalling = :numberCalling')
            ->setParameter(':numberCalling', $numberCalling)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$result) {
            throw new NotFoundHttpException('Вызов #' . $numberCalling . ' не найден');
        }
        return $result;
    }

    public function findOneByOwnerExternalId(string $ownerExternalId): ?Calling
    {
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.ownerExternalId = :ownerExternalId')
            ->setParameter(':ownerExternalId', $ownerExternalId)
            ->getQuery()
            ->getResult();

        return array_shift($result);
    }

    public function findAllByCompletedAtFromPeriod(DatePeriod $period): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere('c.completedAt >= :start')
            ->andWhere('c.completedAt < :end')
            ->setParameter('start', $period->getStartDate())
            ->setParameter('end', $period->getEndDate())
            ->getQuery()
            ->getResult();
    }

    public function findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
        DateTimeImmutable $completedAfter,
        DateTimeImmutable $completedBefore,
        int               $employeeId
    ): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere('c.completedAt >= :after')
            ->andWhere('c.completedAt < :before')
            ->andWhere('c.status = :status')
            ->andWhere('(c.admin = :employeeId OR c.doctor = :employeeId)')
            ->setParameter('after', $completedAfter)
            ->setParameter('before', $completedBefore)
            ->setParameter('employeeId', $employeeId)
            ->setParameter('status', Status::COMPLETED)
            ->getQuery()
            ->getResult();
    }

    public function findAllCompletedByCompletionDateIncludedInPeriod(
        DateTimeImmutable $completedAfter,
        DateTimeImmutable $completedBefore,
    ): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere('c.completedAt >= :after')
            ->andWhere('c.completedAt < :before')
            ->andWhere('c.status = :status')
            ->setParameter('after', $completedAfter)
            ->setParameter('before', $completedBefore)
            ->setParameter('status', Status::COMPLETED)
            ->getQuery()
            ->getResult();
    }

    public function findAllByClientNull(): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere('c.client IS NULL')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult();
    }

    public function findAllByStatus(string $status): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere('c.status >= :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function findAllWhoHasOperator()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.operator IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function getAllCoords(): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->select('c.lat, c.lon')
            ->andWhere('c.lat IS NOT NULL')
            ->andWhere('c.lon IS NOT NULL')
            ->andWhere('c.status = :status')
            ->setParameter('status', Status::COMPLETED)
            ->getQuery()
            ->getResult();
    }

    public function findAllForPartnerApi(
        Partner $partner,
        ?string $sort,
        ?string $direction,
        ?string $search,
        ?string $statuses,
        ?string $completedAtAfter,
        ?string $completedAtBefore,
    ): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($search) {
            $qb->where($qb->expr()->like('LOWER(c.address)', ':search'))
                ->setParameter('search', '%' . $search . '%');
        }

        $qb
            ->andWhere('c.partner = :partner')
            ->setParameter('partner', $partner->getId());

        if ($sort) {
            $qb->orderBy('c.' . $sort, $direction);
        }

        if ($statuses) {
            $statuses = explode(',', $statuses);
            $qb->andWhere($qb->expr()->in('c.status', $statuses));
        }

        if ($completedAtAfter && $completedAtBefore) {
            $qb->andWhere('c.completedAt >= :completedAtAfter')
                ->andWhere('c.completedAt < :completedAtBefore')
                ->setParameter('completedAtAfter', $completedAtAfter)
                ->setParameter('completedAtBefore', $completedAtBefore);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function getHistory(int $callId)
    {
        $coll = $this->getById($callId);

        if ($coll->getStatus() !== Status::ARRIVED && $coll->getStatus() !== Status::TREATING) {
            return [];
        }

        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.client', 'cl')
            ->where('cl.id = :clientId')
            ->andWhere('c.status = :status')
            ->andWhere('c.id != :callId')
            ->setParameter('clientId', $coll->getClient()->getId())
            ->setParameter('status', Status::COMPLETED)
            ->setParameter('callId', $callId);

        return $qb
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findActiveCallByPhone(string $phone): ?Calling
    {
        $workingStatuses = [
            Status::NOT_READY,
            Status::WAITING,
            Status::ASSIGNED,
            Status::ACCEPTED,
            Status::DISPATCHED,
            Status::ARRIVED,
            Status::TREATING,
        ];

        return $this->createQueryBuilder('c')
            ->leftJoin('c.client', 'cl')
            ->where('cl.phone = :phone')
            ->andWhere('c.status IN (:statuses)')
            ->setParameters([
                'phone' => $phone,
                'statuses' => $workingStatuses
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
