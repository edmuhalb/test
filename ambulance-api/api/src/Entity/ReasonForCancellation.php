<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReasonForCancellationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReasonForCancellationRepository::class)]
#[ApiResource(
    routePrefix: '/api/v1',
    normalizationContext: ['groups' => ['reason:read']],
    denormalizationContext: ['groups' => ['reason:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
)]
class ReasonForCancellation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reason:read', 'v1-call:read', 'v1-call:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reason:read', 'reason:write', 'v1-call:read'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isReassignmentRequired = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isReassignmentRequired(): ?bool
    {
        return $this->isReassignmentRequired;
    }

    public function setReassignmentRequired(?bool $isReassignmentRequired): static
    {
        $this->isReassignmentRequired = $isReassignmentRequired;

        return $this;
    }
}
