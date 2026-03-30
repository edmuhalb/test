<?php

declare(strict_types=1);

namespace App\Services;

use App\MkadDistance\Distance;
use Exception;

class TrackerToMkad
{
    public function getDistance(float $lat, float $lon, ?int $cityId = 1): int
    {
        try {
            if ($cityId === 2) {
                $distance = Distance::createSpbKadCalculator(
                    [$lat, $lon]
                )->calculate();
                return (int)$distance;
            }

            $distance = Distance::createMoscowMkadCalculator(
                [$lat, $lon]
            )->calculate();
            return (int)$distance;
        } catch (Exception $e) {
            return 0;
        }
    }
}
