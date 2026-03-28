<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'password_reset_codes')]
#[ORM\Index(columns: ['phone', 'code'], name: 'idx_phone_code')]
class PasswordResetCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 11)]
    private string $phone;

    #[ORM\Column(type: 'string', length: 6)]
    private string $code;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'boolean')]
    private bool $used = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $phone, string $code, int $ttlMinutes = 10)
    {
        $this->phone = $phone;
        $this->code = $code;
        $this->createdAt = new \DateTimeImmutable();
        $this->expiresAt = $this->createdAt->modify("+{$ttlMinutes} minutes");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function markUsed(): void
    {
        $this->used = true;
    }

    public function isExpired(): bool
    {
        return new \DateTimeImmutable() > $this->expiresAt;
    }

    public function isValid(): bool
    {
        return !$this->used && !$this->isExpired();
    }
}
