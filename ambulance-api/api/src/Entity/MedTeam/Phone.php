<?php

declare(strict_types=1);

namespace App\Entity\MedTeam;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MedTeam\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhoneRepository::class)]
#[ApiResource(
    routePrefix: '/api',
    normalizationContext: ['groups' => ['phone:read']],
    denormalizationContext: ['groups' => ['phone:write']],
    openapi: false,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
class Phone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['phone:read', 'phone:write', 'calling:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['phone:read', 'phone:write'])]
    #[Assert\NotBlank(message: 'Телефон обязателен для заполнения.')]
    #[Assert\Regex(
        pattern: '/\d{11}/',
        message: 'Номер телефона должен состоять из 11 цифр.'
    )]
    private ?string $value = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['phone:read', 'phone:write', 'calling:read'])]
    private ?string $externalId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
