<?php

declare(strict_types=1);

namespace App\Services\Call;

use App\Entity\Calling\Calling;
use App\Services\TrackerToMkad;
use App\Services\YaGeolocation\Api;
use Exception;

class DistanceDeterminant
{
    public function __construct(
        private readonly Api $geocodingApi,
        private readonly TrackerToMkad $trackerToMkad
    ) {}

    public function toDetermine(Calling $call): void
    {
        if (($call->getLat() && $call->getLon()) || empty($call->getAddress())) {
            return;
        }

        try {
            $geolocation = $this->geocodingApi->getPositionByAddress($call->getAddress());
            if ($geolocation) {
                $call->setLat($geolocation->getLat());
                $call->setLon($geolocation->getLon());
            }

            $distance = $this->trackerToMkad->getDistance(
                (float)$geolocation->getLat(),
                (float)$geolocation->getLon(),
                $call->getCity()?->getId()
            );

            $call->setMkadDistance($distance);
        } catch (Exception) {
        }
    }
}
