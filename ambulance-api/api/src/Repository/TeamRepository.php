<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team\Status;
use App\Entity\Team\Team;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function save(Team $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getActiveByAdministrator(User $user): Team
    {
        $statuses = [
            Status::ACCEPTED,
            Status::ASSIGNED,
        ];

        $qb = $this->createQueryBuilder('c');

        if (!$checkWordstat = $qb
            ->andWhere('c.administrator = :administrator')
            ->setParameter(':administrator', $user->getId())
            ->andWhere($qb->expr()->in('c.status', $statuses))
            ->getQuery()
            ->getOneOrNullResult()
        ) {
            throw new NotFoundHttpException('Бригада не назначена');
        }
        return $checkWordstat;
    }

    public function remove(Team $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
