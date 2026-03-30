<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AmoCrmToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $accessToken = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $refreshToken = null;

    #[ORM\Column(nullable: true)]
    private ?int $expires = null;

    #[ORM\Column(length: 255)]
    private ?string $baseDomain = null;

    public function __construct(?string $accessToken, ?string $refreshToken, ?int $expires, ?string $baseDomain)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expires = $expires;
        $this->baseDomain = $baseDomain;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getExpires(): ?int
    {
        return $this->expires;
    }

    public function setExpires(?int $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function getBaseDomain(): ?string
    {
        return $this->baseDomain;
    }

    public function setBaseDomain(string $baseDomain): self
    {
        $this->baseDomain = $baseDomain;

        return $this;
    }
}
