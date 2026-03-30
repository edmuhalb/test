<?php

declare(strict_types=1);

namespace App\MkadDistance\Iterface;

use App\MkadDistance\Geometry\DistanceBetweenPoints;

interface DistanceCalculatorStrategy
{
    public function calculate($target, bool $calcByRoutes = true): DistanceBetweenPoints;
}
