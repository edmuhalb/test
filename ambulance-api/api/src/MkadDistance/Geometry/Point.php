<?php

declare(strict_types=1);

namespace App\MkadDistance\Geometry;

class Point
{
    /**
     * @var float
     */
    private $lat;
    /**
     * @var float
     */
    private $lon;

    /**
     * Point constructor.
     */
    public function __construct(float $lat, float $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }

    public function __toString(): string
    {
        return \sprintf('%s,%s', $this->lon, $this->lat);
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLon(): float
    {
        return $this->lon;
    }

    public static function compare(self $p1, self $p2): bool
    {
        return $p1->getLat() === $p2->getLat() && $p1->getLon() === $p2->getLon();
    }

    /**
     * @param array|float[] $coordinate
     */
    public static function createFromArray(array $coordinate): self
    {
        return new self($coordinate[0], $coordinate[1]);
    }
}
