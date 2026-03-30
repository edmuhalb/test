<?php

declare(strict_types=1);

namespace App\Filter\User;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

final class SearchByNameAndPhoneAndEmailFilter extends AbstractFilter
{
    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Search by name, phone',
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
                $queryBuilder->expr()->like($alias . '.name', ':search'),
                $queryBuilder->expr()->like($alias . '.phone', ':search'),
            )
        );

        $parameters = [];
        foreach ($queryBuilder->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = $parameter->getValue();
        }

        $parameters['search'] = '%' . $value . '%';

        $queryBuilder->setParameters($parameters);
    }
}
