<?php

declare(strict_types=1);

namespace App\UseCase\Partner\Create;

class Command
{
    private string $name;
    private ?string $externalId = null;

    public function __construct(string $name, ?string $externalId)
    {
        $this->name = $name;
        $this->externalId = $externalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
