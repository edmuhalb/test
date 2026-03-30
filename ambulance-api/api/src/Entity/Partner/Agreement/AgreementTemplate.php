<?php

declare(strict_types=1);

namespace App\Entity\Partner\Agreement;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Service\Category;
use App\Repository\Partner\Agreement\AgreementTemplateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgreementTemplateRepository::class)]
#[ApiResource(
    routePrefix: '/api',
    normalizationContext: [
        'groups' => [
            'agreement:read',
            'agreement:item:read',
            'service-category:item:read',
        ]],
    denormalizationContext: ['groups' => ['agreement:write']],
    openapi: false,
    order: ['service', 'distance'],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 500,
    paginationMaximumItemsPerPage: 500
)]
class AgreementTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['agreement:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['agreement:read', 'agreement:write'])]
    private ?int $distance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['agreement:read', 'agreement:write'])]
    #[Assert\NotBlank]
    private ?Category $service = null;

    #[ORM\Column]
    #[Groups(['agreement:read', 'agreement:write'])]
    #[Assert\NotBlank]
    private ?float $percent = null;

    #[ORM\Column]
    #[Groups(['agreement:read', 'agreement:write'])]
    #[Assert\NotBlank]
    private ?int $repeatNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getService(): ?Category
    {
        return $this->service;
    }

    public function setService(?Category $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function getRepeatNumber(): ?int
    {
        return $this->repeatNumber;
    }

    public function setRepeatNumber(int $repeatNumber): self
    {
        $this->repeatNumber = $repeatNumber;

        return $this;
    }
}
