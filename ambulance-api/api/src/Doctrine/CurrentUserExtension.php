<?php

declare(strict_types=1);

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Calling\Calling;
use App\Entity\User\User;
use App\Security\UserIdentity;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CurrentUserExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private Security $security,
    )
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Calling::class !== $resourceClass) {
            return;
        }

        if (null === $user = $this->security->getUser()) {
            return;
        }

        if (!$user instanceof UserIdentity) {
            return;
        }

        if (in_array('calls-index', $user->getPermissions())) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(\sprintf('%s.admin = :admin', $rootAlias));
        $queryBuilder->setParameter('admin', $user->getId());
    }

}
