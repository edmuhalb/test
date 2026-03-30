<?php

declare(strict_types=1);

namespace App\Entity\Partner\Agreement;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Partner;
use App\Repository\Partner\Agreement\AgreementRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgreementRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/exchange/agreements',
            normalizationContext: [
                'groups' => [
                    'exchange_agreements:read',
                ],
            ]
        ),
        new Get(
            uriTemplate: '/exchange/agreements/{id}',
        ),
        new GetCollection(
            routePrefix: '/api',
            openapi: false,
            normalizationContext: ['groups' => ['agreement:item:read', 'partner:item:read']]
        ),
        new Post(
            routePrefix: '/api',
            openapi: false,
        ),
        new Get(
            routePrefix: '/api',
            openapi: false,
        ),
    ],
    normalizationContext: [
        'groups' => [
            'agreement:read',
            'agreement:item:read',
            'partner:item:read',
            'service-category:item:read',
        ]],
    denormalizationContext: ['groups' => ['agreement:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(OrderFilter::class, properties: ['startsAt'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'startsAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
class Agreement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['agreement:item:read', 'exchange_partners:read', 'exchange_agreements:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agreements')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiFilter(SearchFilter::class, properties: ['partner.id' => 'exact'])]
    #[Groups(['agreement:item:read', 'agreement:write', 'exchange_agreements:read'])]
    #[Assert\NotBlank]
    private ?Partner $partner = null;

    #[ORM\Column]
    #[Groups(['agreement:item:read', 'agreement:write', 'exchange_partners:read', 'exchange_agreements:read'])]
    #[Assert\NotBlank]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y'])]
    private ?DateTimeImmutable $startsAt = null;

    #[ORM\OneToMany(mappedBy: 'agreement', targetEntity: Row::class, cascade: ['persist'])]
    #[Groups(['agreement:read', 'agreement:write'])]
    #[Assert\Count(min: 1, minMessage: 'В соглашении должна быть хоть одна строка.')]
    private Collection $rows;

    public function __construct()
    {
        $this->rows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function getStartsAt(): ?DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(DateTimeImmutable $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    /**
     * @return Collection<int, Row>
     */
    public function getRows(): Collection
    {
        return $this->rows;
    }

    public function addRow(Row $row): self
    {
        if (!$this->rows->contains($row)) {
            $this->rows->add($row);
            $row->setAgreement($this);
        }

        return $this;
    }

    public function removeRow(Row $row): self
    {
        if ($this->rows->removeElement($row)) {
            // set the owning side to null (unless already changed)
            if ($row->getAgreement() === $this) {
                $row->setAgreement(null);
            }
        }

        return $this;
    }
}
