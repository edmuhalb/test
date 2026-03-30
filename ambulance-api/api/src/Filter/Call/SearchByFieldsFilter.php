<?php

declare(strict_types=1);

namespace App\Filter\Call;

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
                    'description' => 'Search by fio or address or number or phone',
                    'name' => 'search',
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
        if ('search' !== $property) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like($alias . '.address', ':search'),
                $queryBuilder->expr()->like($alias . '.phone', ':search'),
                $queryBuilder->expr()->like($alias . '.fio', ':search'),
                $queryBuilder->expr()->like($alias . '.numberCalling', ':search'),
            )
        )->setParameter('search', '%' . $value . '%');
    }
}
