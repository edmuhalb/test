<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TgChat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TgChat>
 */
class TgChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TgChat::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByChatId(string $chatId): ?TgChat
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.chatId = :chatId')
            ->setParameter(':chatId', $chatId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByUser(int $userId)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :user')
            ->setParameter(':user', $userId)
            ->getQuery()
            ->getResult();
    }

    public function add(TgChat $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
