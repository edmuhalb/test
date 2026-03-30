<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use DomainException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(int $id): User
    {
        /** @var User $user */
        if (!$user = $this->find($id)) {
            throw new EntityNotFoundException(
                \sprintf('User id: %s is not found.', $id)
            );
        }
        return $user;
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function findOneByExternalId(?int $externalId): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.externalId = :externalId')
            ->setParameter(':externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByExternalId(?int $externalId): User
    {
        if ($user =  $this->createQueryBuilder('u')
            ->andWhere('u.externalId = :externalId')
            ->setParameter(':externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult()) {
            return $user;
        }
        throw new DomainException(\sprintf('User externalId "%s" notfound.', $externalId));
    }

    public function getCountUsers(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.externalId)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllByRole(string $role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->andWhere('u.active = 1')
            ->andWhere('u.hideInReports = 0')
            ->setParameter('roles', '%"' . $role . '"%')
            ->orderBy('u.name')
            ->getQuery()
            ->getResult();
    }

    public function findAllByPermission(string $permission, ?int $cityId = null)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.accessRoles', 'r')
            ->leftJoin('r.permissions', 'p')
            ->where('p.id = :permission')
            ->andWhere('u.active = 1')
            ->andWhere('u.hideInReports = 0')
            ->setParameter('permission', $permission);

        if ($cityId) {
            $qb
                ->leftJoin('u.cities', 'c')
                ->andWhere('c.id = :cityId')
                ->setParameter('cityId', $cityId);
        }

        return $qb
            ->orderBy('u.name')
            ->getQuery()
            ->getResult();
    }

    public function findAllByPermissions(array $permissions)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.accessRoles', 'r')
            ->leftJoin('r.permissions', 'p')
            ->where('p.id IN (:permissions)')
            ->andWhere('u.active = 1')
            ->andWhere('u.hideInReports = 0')
            ->setParameter('permissions', $permissions);

        return $qb
            ->orderBy('u.name')
            ->getQuery()
            ->getResult();
    }

    public function findAllActiveByRoleId(int $roleId)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.accessRoles', 'r')
            ->where('r.id = :roleId')
            ->andWhere('u.active = 1')
            ->setParameter('roleId', $roleId);

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByPhone(string $phone): ?User
    {
        /** @var User|null */
        return $this->createQueryBuilder('u')
            ->andWhere('u.phone = :phone')
            ->andWhere('u.active = :active')
            ->setParameter('active', 1)
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
