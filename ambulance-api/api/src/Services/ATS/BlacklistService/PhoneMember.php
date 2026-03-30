<?php

declare(strict_types=1);

namespace App\Services\ATS\BlacklistService;

readonly class PhoneMember
{
    public function __construct(
        public string $id,
        public string $blacklistId,
        public string $number,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['blacklistId'],
            $data['number'],
        );
    }
}
