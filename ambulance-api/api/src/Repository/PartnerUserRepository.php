<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Partner\PartnerUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<PartnerUser>
 *
 * @method PartnerUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method PartnerUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method PartnerUser[]    findAll()
 * @method PartnerUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerUserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerUser::class);
    }

    public function save(PartnerUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(int $id): PartnerUser
    {
        /** @var PartnerUser $user */
        if (!$user = $this->find($id)) {
            throw new EntityNotFoundException(
                \sprintf('User id: %s is not found.', $id)
            );
        }
        return $user;
    }

    public function remove(PartnerUser $entity, bool $flush = false): void
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
        if (!$user instanceof PartnerUser) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function getByPhone(string $phone)
    {
        $user = $this->createQueryBuilder('pu')
            ->select('pu')
            ->andWhere('pu.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()->getOneOrNullResult();

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
