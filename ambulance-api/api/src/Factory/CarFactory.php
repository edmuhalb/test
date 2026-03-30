<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Car;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Car>
 */
final class CarFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Car::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(36),
            'isCaddy' => self::faker()->boolean() ? null : self::faker()->boolean(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(Car $car): void {})
    }
}
