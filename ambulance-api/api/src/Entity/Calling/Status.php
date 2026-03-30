<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use Webmozart\Assert\Assert;

class Status
{
    public const NOT_READY = 'not_ready';
    public const WAITING = 'waiting';

    public const ASSIGNED = 'assigned';
    public const ACCEPTED = 'accepted';

    public const DISPATCHED = 'dispatched';

    public const ARRIVED = 'arrived';
    public const TREATING = 'treating';

    public const COMPLETED = 'completed';
    public const REJECTED = 'rejected';

    public const REPEAT = 'repeat';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::NOT_READY,
            self::WAITING,
            self::ASSIGNED,
            self::ACCEPTED,
            self::DISPATCHED,
            self::REJECTED,
            self::ARRIVED,
            self::TREATING,
            self::COMPLETED,
            self::REPEAT,
        ]);

        $this->name = $name;
    }

    public static function notReady(): self
    {
        return new self(self::NOT_READY);
    }

    public static function waiting(): self
    {
        return new self(self::WAITING);
    }

    public static function assigned(): self
    {
        return new self(self::ASSIGNED);
    }

    public static function accepted(): self
    {
        return new self(self::ACCEPTED);
    }

    public static function dispatched(): self
    {
        return new self(self::DISPATCHED);
    }

    public static function rejected(): self
    {
        return new self(self::REJECTED);
    }

    public static function arrived(): self
    {
        return new self(self::ARRIVED);
    }

    public static function treating(): self
    {
        return new self(self::TREATING);
    }

    public static function completed(): self
    {
        return new self(self::COMPLETED);
    }

    public static function repeat(): self
    {
        return new self(self::REPEAT);
    }

    public function isEqual(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isNotReady(): bool
    {
        return $this->name === self::NOT_READY;
    }

    public function isWaiting(): bool
    {
        return $this->name === self::WAITING;
    }

    public function isAssigned(): bool
    {
        return $this->name === self::ASSIGNED;
    }

    public function isAccepted(): bool
    {
        return $this->name === self::ACCEPTED;
    }

    public function isDispatched(): bool
    {
        return $this->name === self::DISPATCHED;
    }

    public function isRejected(): bool
    {
        return $this->name === self::REJECTED;
    }

    public function isArrived(): bool
    {
        return $this->name === self::ARRIVED;
    }

    public function isCompleted(): bool
    {
        return $this->name === self::COMPLETED;
    }

    public function isTreating(): bool
    {
        return $this->name === self::TREATING;
    }

    public function isRepeat(): bool
    {
        return $this->name === self::REPEAT;
    }

    public function getLabel(): string
    {
        return $this->getLabels()[$this->name];
    }

    private function getLabels(): array
    {
        return [
            self::NOT_READY => 'Не готов',
            self::WAITING => 'Ожидает',
            self::ASSIGNED => 'Назначен',
            self::ACCEPTED => 'Принят',
            self::DISPATCHED => 'Выехали',
            self::REJECTED => 'Отклонен',
            self::ARRIVED => 'Прибыли',
            self::TREATING => 'Лечение',
            self::COMPLETED => 'Завершен',
            self::REPEAT => 'Повтор',
        ];
    }
}
