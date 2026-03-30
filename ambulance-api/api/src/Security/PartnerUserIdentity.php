<?php

declare(strict_types=1);

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class PartnerUserIdentity implements UserInterface, JWTUserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private int $id,
        private string $username,
        private string $name,
        private array $roles,
        private string $password,
        private int $partnerId,
        private string $partnerName,
    ) {}

    public static function createFromPayload($username, array $payload): self
    {
        return new self(
            (int) $payload['id'],
            $username,
            (string) $payload['name'],
            (array) ($payload['roles'] ?? []),
            (string) ($payload['password'] ?? ''),
            (int) $payload['partner_id'],
            (string) $payload['partner_name'],
        );
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPartnerId(): int
    {
        return $this->partnerId;
    }

    public function getPartnerName(): string
    {
        return $this->partnerName;
    }
}
