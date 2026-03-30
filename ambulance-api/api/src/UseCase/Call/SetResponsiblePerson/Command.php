<?php

declare(strict_types=1);

namespace App\UseCase\Call\SetResponsiblePerson;

class Command
{
    public function __construct(
        public readonly string $externalId,
    ) {}
}
