<?php

declare(strict_types=1);

namespace App\Services\Call\CrmCallFetcher;

interface CrmLeadFetcherInterface
{
    public function fetch(string $externalId): ?Lead;
}
