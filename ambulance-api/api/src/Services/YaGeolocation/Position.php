<?php

declare(strict_types=1);

namespace App\Services\YaGeolocation;

class Position
{
    public function __construct(
        private readonly string $lon,
        private readonly string $lat
    ) {}

    public function getLon(): string
    {
        return $this->lon;
    }

    public function getLat(): string
    {
        return $this->lat;
    }
}
