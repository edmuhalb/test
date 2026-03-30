<?php

declare(strict_types=1);

namespace App\Entity\Payroll\KpiDocument;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\Payroll\KpiDocument\KpiDocumentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: KpiDocumentRepository::class)]
#[ORM\Table(name: 'payroll_kpi_documents')]
#[ApiResource(
    shortName: 'Payroll/KpiDocument',
    operations: [
        new GetCollection(),
        new Get(
            normalizationContext: [
                'groups' => [
                    'kpi_document:item:read',
                    'kpi_document:read',
                ],
            ]
        ),
    ],
    routePrefix: '/api/v1',
    normalizationContext: ['groups' => ['kpi_document:item:read']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
)]
class KpiDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['kpi_document:item:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Gedmo\Timestampable(on: 'create')]
    #[Groups(['kpi_document:item:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Gedmo\Timestampable(on: 'update')]
    #[Groups(['kpi_document:item:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: false)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Groups(['kpi_document:item:read'])]
    private DateTimeImmutable $periodStart;

    #[ORM\Column(nullable: false)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Groups(['kpi_document:item:read'])]
    private DateTimeImmutable $periodEnd;

    #[ORM\OneToMany(
        mappedBy: 'document',
        targetEntity: KpiRecord::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[Groups(['kpi_document:read'])]
    private Collection $records;

    public function __construct(
        DateTimeImmutable $periodStart,
        DateTimeImmutable $periodEnd
    ) {
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
        $this->records = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Collection<int, KpiRecord>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function addRecord(KpiRecord $record): static
    {
        if (!$this->records->contains($record)) {
            $this->records->add($record);
        }

        return $this;
    }

    public function removeRecord(KpiRecord $record): static
    {
        $this->records->removeElement($record);

        return $this;
    }

    public function clearRecords(): void
    {
        $this->records->clear();
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getPeriodStart(): DateTimeImmutable
    {
        return $this->periodStart;
    }

    public function getPeriodEnd(): DateTimeImmutable
    {
        return $this->periodEnd;
    }
}
