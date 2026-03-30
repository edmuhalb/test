<?php

declare(strict_types=1);

namespace App\Filter\Partner;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class PartnerCallingCityFilter extends AbstractFilter
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
                'type' => 'integer',
                'required' => false,
                'swagger' => ['description' => 'Filter by city id'],
                'openapi' => ['description' => 'Filter by city id'],
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
        if ('city.id' !== $property) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->join(\sprintf('%s.callings', $alias), 'c')
            ->andWhere('c.city = :city')
            ->setParameter('city', $value);
    }
}
