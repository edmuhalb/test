<?php

declare(strict_types=1);

namespace App\Entity\Role;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\Role\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['permission:read']],
    openapi: false,
    paginationEnabled: false,
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'description'], arguments: ['orderParameterName' => 'order'])]
class Permission
{
    #[ORM\Id]
    #[ORM\Column]
    #[Groups(['permission:read', 'role:read'])]
    private string $id;

    #[ORM\Column(length: 255)]
    #[Groups(['permission:read', 'role:read'])]
    private string $description;

    public function __construct(string $id, string $description)
    {
        $this->id = $id;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
