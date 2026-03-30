<?php

declare(strict_types=1);

namespace App\Filter\Partner;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use DateTime;
use Doctrine\ORM\QueryBuilder;

class PartnerCallingCompletedAtFilter extends AbstractFilter
{
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $unused) {
            $description["{$property}"] = [
                'property' => $property,
                'type' => 'string',
                'required' => false,
                'swagger' => ['description' => 'Filter by date range'],
                'openapi' => ['description' => 'Filter by date range'],
            ];
        }

        return $description;
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ('completedAt' !== $property) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->join(\sprintf('%s.callings', $alias), 'c')
            ->andWhere('c.completedAt > :before')
            ->andWhere('c.completedAt <= :after')
            ->setParameter('before', new DateTime($value['before']))
            ->setParameter('after', new DateTime($value['after']));
    }
}
