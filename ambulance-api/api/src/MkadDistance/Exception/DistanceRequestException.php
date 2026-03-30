<?php

declare(strict_types=1);

namespace App\MkadDistance\Exception;

use App\MkadDistance\Geometry\DistanceBetweenPoints;
use Throwable;

class DistanceRequestException extends DistanceException
{
    /**
     * @var DistanceBetweenPoints|null
     */
    private $lineDistance;

    /**
     * DistanceRequestException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(
        $message = '',
        $code = 0,
        ?Throwable $previous = null,
        ?DistanceBetweenPoints $lineDistance = null
    ) {
        $this->lineDistance = $lineDistance;
        parent::__construct($message, $code, $previous);
    }

    public function getLineDistance(): ?DistanceBetweenPoints
    {
        return $this->lineDistance;
    }
}
