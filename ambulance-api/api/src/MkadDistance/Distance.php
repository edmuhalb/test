<?php

declare(strict_types=1);

namespace App\MkadDistance;

use App\MkadDistance\Geometry\Point;
use App\MkadDistance\Geometry\Polygon\MscMkad;
use App\MkadDistance\Geometry\Polygon\MscMkadJunctions;
use App\MkadDistance\Geometry\Polygon\SpbKad;
use App\MkadDistance\Geometry\Polygon\SpbKadJunctions;
use App\MkadDistance\Iterface\DistanceCalculatorStrategy;
use App\MkadDistance\Strategy\StrategyFactory;
use InvalidArgumentException;

class Distance
{
    /**
     * @var DistanceCalculatorStrategy
     */
    private $calculator;

    /**
     * @var array|Point|string
     */
    private $target;

    public function __construct(DistanceCalculatorStrategy $calculator, $target = null)
    {
        $this->calculator = $calculator;
        $this->target = $target;
    }

    /**
     * Distance in kilometers.
     */
    public function calculate(bool $calByRoutes = true): float
    {
        return round($this->calculator->calculate($this->target, $calByRoutes)->getDistance() / 1000, 2);
    }

    /**
     * @param mixed $target
     * @return static
     * @throws InvalidArgumentException
     */
    public static function createMoscowMkadCalculator($target, array $options = []): self
    {
        $strategyFactory = new StrategyFactory($options);
        return new self(
            $strategyFactory->create($target, new MscMkad(), new MscMkadJunctions()),
            $target
        );
    }

    /**
     * @param mixed $target
     * @throws InvalidArgumentException
     */
    public static function createSpbKadCalculator($target, array $options = []): self
    {
        $strategyFactory = new StrategyFactory($options);
        return new self(
            $strategyFactory->create($target, new SpbKad(), new SpbKadJunctions()),
            $target
        );
    }
}
