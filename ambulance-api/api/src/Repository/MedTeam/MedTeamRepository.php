<?php

declare(strict_types=1);

namespace App\Repository\MedTeam;

use App\Entity\MedTeam\MedTeam;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<MedTeam>
 *
 * @method MedTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedTeam[]    findAll()
 * @method MedTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedTeam::class);
    }

    public function save(MedTeam $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MedTeam $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getById($id): MedTeam
    {
        $result = $this->find($id);

        if (!$result) {
            throw new NotFoundHttpException('Команда #' . $id . ' не найдена');
        }
        return $result;
    }

    public function getLastWorkByNumber(mixed $team): ?MedTeam
    {
        $data = $this->createQueryBuilder('t')
            ->andWhere('t.status = :work')
            ->andWhere('t.phone = :val')
            ->setParameter('val', $team)
            ->setParameter('work', 'work')
            ->orderBy('t.plannedStartAt', 'DESC')
            ->getQuery()
            ->getResult();

        $data = $data === null ? [] : $data;

        return array_shift($data);
    }

    public function findByPlanned(DateTimeInterface $startDate, DateTimeInterface $endDate)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.plannedStartAt >= :plannedStartAtAfter')
            ->andWhere('t.plannedStartAt < :plannedStartAtBefore')
            ->andWhere('t.status = :status')
            ->setParameter('plannedStartAtAfter', $startDate)
            ->setParameter('plannedStartAtBefore', $endDate)
            ->setParameter('status', 'completed')
            ->orderBy('t.plannedStartAt')
            ->getQuery()
            ->getResult();
    }

    public function findByPlannedEmployee(DateTimeInterface $startDate, DateTimeInterface $endDate, int $employeeId)
    {
        $qb = $this->createQueryBuilder('t');

        $orConditions = $qb->expr()->orX(
        // Смены, которые начались в заданном периоде
            $qb->expr()->andX(
                't.plannedStartAt >= :startDate',
                't.plannedStartAt < :endDate'
            ),
            // Смены, которые начались до периода, но закончились в нём
            $qb->expr()->andX(
                't.plannedFinishAt > :startDate',
                't.plannedFinishAt <= :endDate',
                't.plannedStartAt < :startDate'
            )
        );

        return $qb
            ->andWhere($orConditions)
            ->andWhere('t.status = :status')
            ->andWhere('(t.admin = :employeeId OR t.doctor = :employeeId)')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('status', 'completed')
            ->setParameter('employeeId', $employeeId)
            ->orderBy('t.plannedStartAt')
            ->getQuery()
            ->getResult();
    }

    public function findForReportByPlanned(DateTimeInterface $startDate, DateTimeInterface $endDate)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.plannedStartAt >= :plannedStartAtAfter')
            ->andWhere('t.plannedStartAt < :plannedStartAtBefore')
            ->setParameter('plannedStartAtAfter', $startDate)
            ->setParameter('plannedStartAtBefore', $endDate)
            ->orderBy('t.plannedStartAt')
            ->getQuery()
            ->getResult();
    }

    public function findAllWorking(): array
    {
        $data = $this->createQueryBuilder('t')
            ->andWhere('t.status = :work')
            ->setParameter('work', 'work')
            ->getQuery()
            ->getResult();

        return $data === null ? [] : $data;
    }
}
