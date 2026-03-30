<?php

declare(strict_types=1);

namespace App\MkadDistance\Geometry;

class DistanceBetweenPoints
{
    /**
     * @var Point
     */
    private $from;
    /**
     * @var Point
     */
    private $to;
    /**
     * Расстояние.
     * @var float
     */
    private $distance;

    /**
     * DistanceBetweenPoints constructor.
     */
    public function __construct(Point $from, Point $to, float $distance)
    {
        $this->from = $from;
        $this->to = $to;
        $this->distance = $distance;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function getFrom(): Point
    {
        return $this->from;
    }

    public function getTo(): Point
    {
        return $this->to;
    }
}
