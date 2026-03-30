<?php

declare(strict_types=1);

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final readonly class UserIdentity implements JWTUserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private int $id,
        private string $username,
        private string $name,
        private array $permissions,
        private string $password,
        private ?string $avatar,
        private ?string $position,
        private int $active,
    ) {}

    public static function createFromPayload($username, array $payload): self
    {
        return new self(
            (int)$payload['id'],
            $username,
            (string)$payload['name'],
            (array)$payload['permissions'],
            (string)$payload['password'],
            (string)$payload['avatar'],
            (string)$payload['position'],
            (int)$payload['active']
        );
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->permissions;
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

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function getActive(): int
    {
        return $this->active;
    }
}
