<?php

declare(strict_types=1);

namespace App\Entity\Money;

use JsonSerializable;
use Webmozart\Assert\Assert;

readonly class Currency implements JsonSerializable
{
    private string $code;

    public function __construct(string $code)
    {
        Assert::notEmpty($code);
        Assert::length($code, 3, 'Currency code must be 3 characters');

        /** @var string $code */
        $code = mb_strtoupper($code);
        $this->code = $code;
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isEqual(self $other): bool
    {
        return $this->code === $other->code;
    }

    public function jsonSerialize(): string
    {
        return $this->code;
    }
}
