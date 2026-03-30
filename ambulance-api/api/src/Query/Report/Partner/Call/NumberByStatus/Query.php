<?php

declare(strict_types=1);

namespace App\Query\Report\Partner\Call\NumberByStatus;

class Query
{
    public function __construct(
        public readonly int $partnerId,
    ) {}
}
