<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            routePrefix: '/api',
            openapi: false,
        ),
        new GetCollection(
            uriTemplate: '/exchange/cars',
        ),
        new Post(
            routePrefix: '/api',
            openapi: false,
        ),
        new Get(
            routePrefix: '/api',
            openapi: false,
        ),
        new Put(
            routePrefix: '/api',
            openapi: false,
        ),
        new Delete(
            routePrefix: '/api',
            openapi: false,
        ),
    ],
    normalizationContext: ['groups' => ['car:read']],
    denormalizationContext: ['groups' => ['car:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car:read', 'v1:shift:item:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['car:read', 'car:write', 'v1:shift:item:read'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['car:read', 'car:write'])]
    private ?bool $isCaddy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isIsCaddy(): ?bool
    {
        return $this->isCaddy;
    }

    public function setIsCaddy(?bool $isCaddy): self
    {
        $this->isCaddy = $isCaddy;

        return $this;
    }
}
