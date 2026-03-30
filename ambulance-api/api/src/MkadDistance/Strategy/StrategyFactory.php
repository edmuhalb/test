<?php

declare(strict_types=1);

namespace App\MkadDistance\Strategy;

use App\MkadDistance\Geometry\Point;
use App\MkadDistance\Geometry\Polygon;
use App\MkadDistance\Iterface\DistanceCalculatorStrategy;
use InvalidArgumentException;

class StrategyFactory
{
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @param mixed $target
     * @throws InvalidArgumentException
     */
    public function create(
        $target,
        Polygon $basePolygon,
        Polygon $junctionsPolygon
    ): ?DistanceCalculatorStrategy {
        $cache = $this->options['cache'] ?? null;

        if ($target instanceof Point) {
            return new PointDistanceCalculator($basePolygon, $junctionsPolygon, $cache);
        }

        if (\is_array($target)) {
            return new ArrayDistanceCalculator($basePolygon, $junctionsPolygon, $cache);
        }

        if (\is_string($target) && isset($this->options['yandexGeoCoderApiKey'])) {
            return new AddressDistanceCalculator(
                (string)$this->options['yandexGeoCoderApiKey'],
                $basePolygon,
                $junctionsPolygon,
                $cache
            );
        }

        throw new InvalidArgumentException('Target param incorrect type');
    }
}
