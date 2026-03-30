<?php

declare(strict_types=1);

namespace App\Entity\Team;

use Webmozart\Assert\Assert;

class Status
{
    public const ASSIGNED = 'assigned';
    public const ACCEPTED = 'accepted';

    public const REJECTED = 'rejected';

    public const COMPLETED = 'completed';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::ASSIGNED,
            self::ACCEPTED,
            self::REJECTED,
            self::COMPLETED,
        ]);

        $this->name = $name;
    }

    public static function assigned(): self
    {
        return new self(self::ASSIGNED);
    }

    public static function accepted(): self
    {
        return new self(self::ACCEPTED);
    }

    public static function rejected(): self
    {
        return new self(self::REJECTED);
    }

    public static function completed(): self
    {
        return new self(self::COMPLETED);
    }

    public function isEqual(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isAssigned(): bool
    {
        return $this->name === self::ASSIGNED;
    }

    public function isAccepted(): bool
    {
        return $this->name === self::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->name === self::REJECTED;
    }

    public function isCompleted(): bool
    {
        return $this->name === self::COMPLETED;
    }
}
