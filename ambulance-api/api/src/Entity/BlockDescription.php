<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\BlockDescriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BlockDescriptionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Patch(),
    ],
    routePrefix: '/api/v1',
    normalizationContext: ['groups' => ['block_description:read']],
    denormalizationContext: ['groups' => ['block_description:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
)]
class BlockDescription
{
    #[ORM\Id]
    #[Groups(['block_description:read'])]
    #[ORM\Column(type: Types::STRING)]
    private string $id;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['block_description:read', 'block_description:write'])]
    private ?string $value = null;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }
}
