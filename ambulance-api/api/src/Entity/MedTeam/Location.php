<?php

declare(strict_types=1);

namespace App\Entity\MedTeam;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MedTeam\LocationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    uriTemplate: '/team-locations',
    routePrefix: '/api',
    normalizationContext: ['groups' => ['med-team-location:read']],
    denormalizationContext: ['groups' => ['med-team-location:write']],
    openapi: false,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['med-team-location:write'])]
    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MedTeam $medTeam = null;

    #[Groups(['med-team-location:write', 'med-team:read'])]
    #[ORM\Column(length: 16)]
    private string $lon;
    #[Groups(['med-team-location:write', 'med-team:read'])]
    #[ORM\Column(length: 16)]
    private string $lat;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    #[Groups(['med-team:read'])]
    private ?DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedTeam(): ?MedTeam
    {
        return $this->medTeam;
    }

    public function setMedTeam(?MedTeam $medTeam): self
    {
        $this->medTeam = $medTeam;

        return $this;
    }

    public function getLat(): string
    {
        return $this->lat;
    }

    public function setLat(string $lat): void
    {
        $this->lat = $lat;
    }

    public function getLon(): string
    {
        return $this->lon;
    }

    public function setLon(string $lon): void
    {
        $this->lon = $lon;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
