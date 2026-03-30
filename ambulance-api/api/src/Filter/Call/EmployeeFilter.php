<?php

declare(strict_types=1);

namespace App\Filter\Call;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

final class EmployeeFilter extends AbstractFilter
{
    public function getDescription(string $resourceClass): array
    {
        return [
            'employee' => [
                'property' => null,
                'type' => 'integer',
                'required' => false,
                'swagger' => [
                    'description' => 'Search by admin.id or doctor.id or operator.id',
                    'name' => 'search',
                    'type' => 'integer',
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
        if ('employee' !== $property) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq($alias . '.admin', ':employee'),
                $queryBuilder->expr()->eq($alias . '.doctor', ':employee'),
                $queryBuilder->expr()->eq($alias . '.operator', ':employee'),
            )
        )->setParameter('employee', $value);
    }
}
