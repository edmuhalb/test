<?php

declare(strict_types=1);

namespace App\Entity\Hospital;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Calling\Calling;
use App\Entity\MediaObject;
use App\Entity\Partner;
use App\Entity\User\User;
use App\Filter\Hospital\SearchByNameAndPhoneFilter;
use App\Repository\Hospital\HospitalRepository;
use App\State\HospitalProcessor;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HospitalRepository::class)]
#[ORM\Table(name: 'hospital_hospitals')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(
            normalizationContext: [
                'groups' => [
                    'hospital:read',
                    'hospital:detail:read',
                    'partner:item:read',
                    'media_object:read',
                    'media_object:read:image',
                ],
            ],
        ),
        new Put(),
        new Delete(),
        new Patch(),
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['hospital:read', 'partner:item:read']],
    denormalizationContext: ['groups' => ['hospital:write']],
    openapi: false,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    processor: HospitalProcessor::class
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'createdAt'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(
    SearchByNameAndPhoneFilter::class,
    properties: ['search']
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'createdAt' => DateFilterInterface::EXCLUDE_NULL,
        'updatedAt' => DateFilterInterface::EXCLUDE_NULL,
        'hospitalizedAt' => DateFilterInterface::EXCLUDE_NULL,
        'dischargedAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'status' => 'exact',
        'partner.id' => 'exact',
        'clinic.id' => 'exact',
        'hospitalizedBy.id' => 'exact',
        'dischargedBy.id' => 'exact',
    ]
)]
class Hospital
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hospital:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?string $external = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?string $fio = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hospital:read', 'hospital:write'])]
    #[Assert\Choice(choices: [
        'assigned',
        'inpatient',
        'completed',
        'cancelled',
    ])]
    private ?string $status = 'assigned';

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    #[Assert\Choice(choices: [
        'alcohol',
        'drugs',
        'alcohol-drugs',
        'gambler',
        'psycho',
    ])]
    private ?string $nosology = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?Partner $partner = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read'])]
    private ?int $amount = null;

    #[ORM\ManyToOne]
    #[Groups(['hospital:read', 'hospital:write', 'hospital:detail:read'])]
    private ?Calling $owner = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?int $prepayment = null;

    #[ORM\ManyToOne(inversedBy: 'hospitals')]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?Clinic $clinic = null;

    #[ORM\Column(length: 11, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    #[Assert\Regex(
        pattern: '/\d{11}/',
        message: 'Номер телефона должен состоять из 11 цифр.'
    )]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?int $additionalAmount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?int $mainAmount = null;

    #[ORM\ManyToOne]
    #[Gedmo\Blameable(on: 'create')]
    #[Groups(['hospital:read'])]
    private ?User $createdBy = null;

    #[ORM\ManyToOne]
    #[Gedmo\Blameable(on: 'update')]
    #[Groups(['hospital:read'])]
    private ?User $updatedBy = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?DateTimeImmutable $hospitalizedAt = null;

    #[ORM\ManyToOne]
    #[Groups(['hospital:read'])]
    private ?User $hospitalizedBy = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?DateTimeImmutable $dischargedAt = null;

    #[ORM\ManyToOne]
    #[Groups(['hospital:read'])]
    private ?User $dischargedBy = null;

    #[ORM\ManyToMany(targetEntity: MediaObject::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'hospital_images')]
    #[ORM\JoinColumn(name: 'hospital_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'media_object_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['hospital:read', 'hospital:write', 'hospital:detail:read'])]
    private Collection $images;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    #[Groups(['hospital:read', 'hospital:write', 'hospital:detail:read'])]
    private int $partnerReward = 0;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternal(): ?string
    {
        return $this->external;
    }

    public function setExternal(?string $external): self
    {
        $this->external = $external;

        return $this;
    }

    public function getFio(): ?string
    {
        return $this->fio;
    }

    public function setFio(?string $fio): self
    {
        $this->fio = $fio;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getNosology(): ?string
    {
        return $this->nosology;
    }

    public function setNosology(?string $nosology): self
    {
        $this->nosology = $nosology;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount === null ? 0 : $this->amount;
    }

    public function getOwner(): ?Calling
    {
        return $this->owner;
    }

    public function setOwner(?Calling $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getPrepayment(): ?int
    {
        return $this->prepayment;
    }

    public function setPrepayment(?int $prepayment): self
    {
        $this->prepayment = $prepayment;

        return $this;
    }

    public function getClinic(): ?Clinic
    {
        return $this->clinic;
    }

    public function setClinic(?Clinic $clinic): self
    {
        $this->clinic = $clinic;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone ? preg_replace('/\D/', '', $phone) : null;

        return $this;
    }

    public function getAdditionalAmount(): int
    {
        return $this->additionalAmount === null ? 0 : $this->additionalAmount;
    }

    public function setAdditionalAmount(?int $additionalAmount): self
    {
        $this->additionalAmount = $additionalAmount;

        $this->calcAmount();

        return $this;
    }

    public function getMainAmount(): int
    {
        return $this->mainAmount === null ? 0 : $this->mainAmount;
    }

    public function setMainAmount(?int $mainAmount): self
    {
        $this->mainAmount = $mainAmount;

        $this->calcAmount();

        return $this;
    }

    #[Groups(['hospital:read'])]
    public function getCreatedAt(): string
    {
        if ($this->createdAt === null || $this->createdAt === new DateTime('0000-00-00 00:00:00')) {
            return '';
        }
        return $this->createdAt->format('d.m.Y H:i:s');
    }

    #[Groups(['hospital:read'])]
    public function getUpdatedAt(): string
    {
        if ($this->updatedAt === null || $this->updatedAt === new DateTime('0000-00-00 00:00:00')) {
            return '';
        }
        return $this->updatedAt->format('d.m.Y H:i:s');
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getHospitalizedAt(): ?string
    {
        return $this->hospitalizedAt?->format('d.m.Y H:i:s');
    }

    #[Groups(['hospital:read'])]
    public function getHospitalized(): ?DateTimeImmutable
    {
        return $this->hospitalizedAt;
    }

    public function setHospitalizedAt(?DateTimeImmutable $hospitalizedAt): self
    {
        $this->hospitalizedAt = $hospitalizedAt;

        return $this;
    }

    public function getHospitalizedBy(): ?User
    {
        return $this->hospitalizedBy;
    }

    public function setHospitalizedBy(?User $hospitalizedBy): self
    {
        $this->hospitalizedBy = $hospitalizedBy;

        return $this;
    }

    public function getDischargedAt(): ?string
    {
        return $this->dischargedAt?->format('d.m.Y H:i:s');
    }

    #[Groups(['hospital:read'])]
    public function getDischarged(): ?DateTimeImmutable
    {
        return $this->dischargedAt;
    }

    public function setDischargedAt(?DateTimeImmutable $dischargedAt): self
    {
        $this->dischargedAt = $dischargedAt;

        return $this;
    }

    public function getDischargedBy(): ?User
    {
        return $this->dischargedBy;
    }

    public function setDischargedBy(?User $dischargedBy): self
    {
        $this->dischargedBy = $dischargedBy;

        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(MediaObject $mediaObject): self
    {
        if (!$this->images->contains($mediaObject)) {
            $this->images->add($mediaObject);
        }

        return $this;
    }

    public function removeImage(MediaObject $mediaObject): self
    {
        $this->images->removeElement($mediaObject);

        return $this;
    }

    #[Groups(['hospital:detail:read'])]
    public function getOwnerData(): array
    {
        return [
            'id' => $this->owner?->getId(),
            'name' => $this->owner?->getName(),
            'phone' => $this->owner?->getOriginalPhone(),
            'age' => $this->owner?->getAge(),
            'stationary' => $this->getOwnerStationary(),
        ];
    }

    public function getPartnerReward(): int
    {
        return $this->partnerReward;
    }

    public function setPartnerReward(int $partnerReward): self
    {
        $this->partnerReward = $partnerReward;

        return $this;
    }

    private function calcAmount(): void
    {
        $mainAmount = $this->mainAmount === null ? 0 : $this->mainAmount;
        $additionalAmount = $this->additionalAmount === null ? 0 : $this->additionalAmount;

        $this->amount = $mainAmount + $additionalAmount;
    }

    private function getOwnerStationary(): ?array
    {
        if (!$this->owner) {
            return null;
        }

        foreach ($this->owner->getServices() as $row) {
            if ($row->isStationary()) {
                return [
                    'id' => $row->getService()->getId(),
                    'name' => $row->getService()->getName(),
                    'price' => $row->getPrice(),
                    'plannedPrice' => $row->getPlannedPrice(),
                    'description' => $row->getDescription(),
                    'files' => $row->getFiles(),
                ];
            }
        }

        return null;
    }
}
