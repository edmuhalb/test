<?php

declare(strict_types=1);

namespace App\Filter\User;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class SearchByPermissionsFilter extends AbstractFilter
{
    public function __construct(
        protected ManagerRegistry $managerRegistry,
        protected EntityManagerInterface $em,
        ?LoggerInterface $logger = null,
        protected ?array $properties = null,
        protected ?NameConverterInterface $nameConverter = null,
    ) {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'permissions' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Search by permissions',
                    'name' => 'permissions',
                    'type' => 'string',
                ],
            ],
        ];
    }

    /** @param string $value */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ('permissions' !== $property) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $subQuery = $this->em->createQueryBuilder();
        $subQuery->select('u.id')
            ->from(User::class, 'u')
            ->innerJoin('u.accessRoles', 'r')
            ->innerJoin('r.permissions', 'p')
            ->where($subQuery->expr()->in('p.id', ':permission_ids'));

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in($alias . '.id', $subQuery->getDQL())
        );

        $parameters = [];
        foreach ($queryBuilder->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = $parameter->getValue();
        }

        $parameters['permission_ids'] = $value;

        $queryBuilder->setParameters($parameters);
    }
}
