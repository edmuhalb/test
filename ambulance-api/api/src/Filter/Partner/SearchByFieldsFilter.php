<?php

declare(strict_types=1);

namespace App\Filter\Partner;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

final class SearchByFieldsFilter extends AbstractFilter
{
    public function getDescription(string $resourceClass): array
    {
        return [
            'name' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Search by name or fulName or phone or email',
                    'name' => 'name',
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
        if ('name' !== $property) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like($alias . '.name', ':search'),
                $queryBuilder->expr()->like($alias . '.fullName', ':search'),
                $queryBuilder->expr()->like($alias . '.phone', ':search'),
                $queryBuilder->expr()->like($alias . '.email', ':search'),
            )
        )->setParameter('search', '%' . $value . '%');
    }
}
