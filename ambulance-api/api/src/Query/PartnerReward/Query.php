<?php

declare(strict_types=1);

namespace App\Query\PartnerReward;

use DateTimeImmutable;

class Query
{
    public function __construct(
        public readonly DateTimeImmutable $time,
        public readonly ?int $partnerId,
        public readonly ?int $serviceId,
        public readonly int $repeat,
        public readonly int $distance
    ) {}
}
