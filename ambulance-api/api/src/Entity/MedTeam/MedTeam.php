<?php

declare(strict_types=1);

namespace App\Entity\MedTeam;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\MedTeam\SendSms;
use App\Entity\AdministratorReport;
use App\Entity\Base;
use App\Entity\Calling\Calling;
use App\Entity\CallType;
use App\Entity\Car;
use App\Entity\City;
use App\Entity\User\User;
use App\Repository\MedTeam\MedTeamRepository;
use App\State\MedTeam\PatchProcessor;
use App\State\MedTeam\PostProcessor;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedTeamRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            outputFormats: ['json' => ['application/json']],
            routePrefix: '/api/v1',
            shortName: 'Shift',
            normalizationContext: ['groups' => ['v1:shift:item:read']]
        ),
        new Post(
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
            routePrefix: '/api/v1',
            shortName: 'Shift',
            normalizationContext: ['groups' => ['v1:shift:item:read', 'v1:shift:read', 'media_object:read']],
            denormalizationContext: ['groups' => ['v1:shift:write']],
        ),
        new Get(
            outputFormats: ['json' => ['application/json']],
            routePrefix: '/api/v1',
            shortName: 'Shift',
            normalizationContext: ['groups' => ['v1:shift:item:read', 'v1:shift:read', 'media_object:read']],
        ),
        new Patch(
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
            routePrefix: '/api/v1',
            shortName: 'Shift',
            normalizationContext: ['groups' => ['v1:shift:item:read', 'v1:shift:read', 'media_object:read']],
            denormalizationContext: ['groups' => ['v1:shift:write']],
            processor: PatchProcessor::class,
        ),
        new Delete(
            routePrefix: '/api/v1',
            shortName: 'Shift'
        ),

        new GetCollection(uriTemplate: '/exchange/med_teams'),

        new GetCollection(
            routePrefix: '/api',
            openapi: true,
        ),
        new Post(
            routePrefix: '/api',
            openapi: false,
            processor: PostProcessor::class,
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
        new Patch(
            routePrefix: '/api',
            openapi: false,
            processor: PostProcessor::class,
        ),
        new Post(
            uriTemplate: '/med_teams/{id}/send-sms',
            routePrefix: '/api',
            controller: SendSms::class,
            openapi: false,
            name: 'med_teams-send_sms',
        ),
    ],
    normalizationContext: ['groups' => ['med-team:read', 'user:item:read', 'phone:read', 'car:read', 'base:read']],
    denormalizationContext: ['groups' => ['med-team:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'plannedStartAt' => DateFilterInterface::EXCLUDE_NULL,
        'plannedFinishAt' => DateFilterInterface::EXCLUDE_NULL,
        'startedAt' => DateFilterInterface::EXCLUDE_NULL,
        'completedAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['plannedStartAt'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'status' => 'exact',
        'city.id' => 'exact',
        'admin.id' => 'exact',
        'doctor.id' => 'exact',
        'callType' => 'exact',
    ])]
class MedTeam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'med-team:read',
        'administrator_report:read',
        'calling:read',
        'exchange_calling:read',
        'v1:shift:item:read',
        'administrator_report:read',
        'ambulance_call_log:read',
    ])]
    private ?int $id = null;

    #[Groups(['med-team:write'])]
    private bool $sendSms = false;

    #[ORM\Column]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'v1:shift:item:read',
        'administrator_report:read'
    ])]
    #[Assert\NotNull]
    private ?DateTimeImmutable $plannedStartAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'exchange_calling:read',
        'v1:shift:item:read',
    ])]
    #[ApiProperty(
        openapiContext: [
            'description' => 'Время начала смены.',
        ]
    )]
    private ?DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'exchange_calling:read',
        'v1:shift:item:read',
    ])]
    #[ApiProperty(
        openapiContext: [
            'description' => 'Время окончания смены.',
        ]
    )]
    private ?DateTimeImmutable $completedAt = null;

    #[ORM\Column(length: 32)]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'calling:read',
        'exchange_calling:read',
        'v1:shift:item:read',
        'v1:shift:write',
        'administrator_report:read',
        'ambulance_call_log:read',
    ])]
    #[Assert\Choice(choices: [
        'draft',
        'scheduled',
        'work',
        'completed',
        'cancelled',
    ])]
    private string $status = 'scheduled';

    #[ORM\ManyToOne]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:read',
        'v1:shift:item:read',
        'administrator_report:read',
        'ambulance_call_log:read',
    ])]
    private ?User $admin = null;

    #[ORM\ManyToOne]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'v1:shift:item:read',
        'ambulance_call_log:read',
    ])]
    private ?User $doctor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'calling:read',
    ])]
    private ?Phone $phone = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['med-team:read', 'med-team:write', 'v1:shift:item:read', 'administrator_report:read'])]
    #[Assert\NotNull]
    private ?DateTimeImmutable $plannedFinishAt = null;

    #[ORM\ManyToOne]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'v1:shift:item:read',
    ])]
    private ?Base $base = null;

    #[ORM\ManyToOne]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'v1:shift:item:read',
    ])]
    private ?Car $car = null;

    #[ORM\OneToMany(mappedBy: 'medTeam', targetEntity: Location::class)]
    #[Groups(['med-team:read'])]
    private Collection $locations;

    #[ORM\ManyToOne]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'v1:shift:item:read',
        'ambulance_call_log:read',
    ])]
    private ?User $driver = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'v1:shift:item:read',
    ])]
    private ?DateTimeImmutable $plannedDutyStartAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'med-team:read',
        'med-team:write',
        'administrator_report:detail:read',
        'v1:shift:item:read',
    ])]
    private ?DateTimeImmutable $plannedDutyFinishAt = null;

    #[Assert\Choice(choices: [
        'daytime', // дневная *
        'daytime13', // дневная 13*
        'daytime14', // дневная 14*
        'daytime15', // дневная 15*
        'night',   // ночная
        'evening', // вечерняя
        'day',     // суточная
        'arbitrary',     // произвольная
    ])]
    #[Groups(['med-team:read', 'med-team:write', 'v1:shift:item:read'])]
    #[ORM\Column(length: 32, nullable: true, options: ['default' => 'daytime'])]
    private ?string $type = 'daytime';

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Calling::class)]
    private Collection $callings;

    #[ORM\ManyToOne]
    #[Groups(['med-team:read', 'med-team:write', 'v1:shift:item:read'])]
    private ?City $city = null;

    #[Groups([
        'v1:shift:read',
        'v1:shift:item:read',
        'v1:shift:write',
    ])]
    #[ORM\OneToOne(inversedBy: 'shift', cascade: ['persist', 'remove'])]
    private ?AdministratorReport $transportReport = null;

    #[ORM\Column(length: 255, options: ['default' => CallType::NARCOLOGY])]
    #[Groups([
        'v1:shift:read',
        'v1:shift:item:read',
        'v1:shift:write',
        'med-team:read',
        'med-team:write',
    ])]
    private ?string $callType;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->callings = new ArrayCollection();
        $this->callType = CallType::NARCOLOGY;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlannedStartAt(): ?DateTimeImmutable
    {
        return $this->plannedStartAt;
    }

    public function getPlannedHours(): int
    {
        $interval = $this->plannedStartAt->diff($this->plannedFinishAt);
        $hours = $interval->h;
        return $hours + ($interval->days * 24);
    }

    public function getDay(): int
    {
        return (int)$this->plannedStartAt->format('d');
    }

    public function setPlannedStartAt(DateTimeImmutable $plannedStartAt): self
    {
        $this->plannedStartAt = $plannedStartAt;

        return $this;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;

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

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getDoctor(): ?User
    {
        return $this->doctor;
    }

    public function setDoctor(?User $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getPhone(): ?Phone
    {
        if (!$this->phone) {
            $phone = new Phone();
            $phone->setId(0);
            $phone->setValue('79000000000');
            $phone->setExternalId('0');
            return $phone;
        }

        return $this->phone;
    }

    public function setPhone(?Phone $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPlannedFinishAt(): ?DateTimeImmutable
    {
        return $this->plannedFinishAt;
    }

    public function setPlannedFinishAt(?DateTimeImmutable $plannedFinishAt): self
    {
        $this->plannedFinishAt = $plannedFinishAt;

        return $this;
    }

    public function getBase(): ?Base
    {
        return $this->base;
    }

    public function setBase(?Base $base): self
    {
        $this->base = $base;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): self
    {
        $this->car = $car;

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setMedTeam($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getMedTeam() === $this) {
                $location->setMedTeam(null);
            }
        }

        return $this;
    }

    public function getDriver(): ?User
    {
        return $this->driver;
    }

    public function setDriver(?User $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getPlannedDutyStartAt(): ?DateTimeImmutable
    {
        return $this->plannedDutyStartAt;
    }

    public function setPlannedDutyStartAt(?DateTimeImmutable $plannedDutyStartAt): void
    {
        $this->plannedDutyStartAt = $plannedDutyStartAt;
    }

    public function getPlannedDutyFinishAt(): ?DateTimeImmutable
    {
        return $this->plannedDutyFinishAt;
    }

    public function setPlannedDutyFinishAt(?DateTimeImmutable $plannedDutyFinishAt): void
    {
        $this->plannedDutyFinishAt = $plannedDutyFinishAt;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDutyHours(): int
    {
        if (!$this->plannedDutyStartAt || !$this->plannedDutyFinishAt) {
            return 0;
        }
        $interval = $this->plannedDutyStartAt->diff($this->plannedDutyFinishAt);
        $hours = $interval->h;
        return $hours + ($interval->days * 24);
    }

    /**
     * @return Collection<int, Calling>
     */
    public function getCallings(): Collection
    {
        return $this->callings;
    }

    public function addCalling(Calling $calling): self
    {
        if (!$this->callings->contains($calling)) {
            $this->callings->add($calling);
            $calling->setTeam($this);
        }

        return $this;
    }

    public function removeCalling(Calling $calling): self
    {
        if ($this->callings->removeElement($calling)) {
            // set the owning side to null (unless already changed)
            if ($calling->getTeam() === $this) {
                $calling->setTeam(null);
            }
        }

        return $this;
    }

    #[Groups(['med-team:read'])]
    public function getCountHours(): ?int
    {
        if (!$this->startedAt || !$this->completedAt) {
            return null;
        }
        $diff = $this->completedAt->diff($this->startedAt);

        return $diff->days * 24 + $diff->h;
    }

    public function isSendSms(): bool
    {
        return $this->sendSms;
    }

    public function setSendSms(bool $sendSms): void
    {
        $this->sendSms = $sendSms;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getOverTimeHOurs(): int
    {
        if (!$this->plannedFinishAt || !$this->completedAt) {
            return 0;
        }
        if ($this->completedAt <= $this->plannedFinishAt) {
            return 0;
        }
        $diff = $this->completedAt->diff($this->plannedFinishAt);
        $hours = $diff->h;
        return $hours + ($diff->days * 24);
    }

    public function getTypeTitle(): string
    {
        $titles = [
            'daytime' => 'дневная',
            'daytime13' => 'дневная 13',
            'daytime14' => 'дневная 14',
            'daytime15' => 'дневная 15',
            'night' => 'ночная',
            'evening' => 'вечерняя',
            'day' => 'суточная',
            'arbitrary' => 'произвольная',
        ];

        if (isset($titles[$this->type])) {
            return $titles[$this->type];
        }
        return 'произвольная';
    }

    public function getAdminPrice(): int
    {
        if (!$this->driver) {
            return $this->getTypePrice();
        }

        $titles = [
            'daytime' => 0,
            'daytime13' => 750,
            'daytime14' => 750,
            'daytime15' => 750,
            'night' => 0,
            'evening' => 0,
            'day' => 1250,
            'arbitrary' => 0,
        ];

        if (isset($titles[$this->type])) {
            return $titles[$this->type];
        }
        return 0;
    }

    public function getDoctorPrice(): int
    {
        if (!$this->driver) {
            return $this->getTypePrice();
        }

        $titles = [
            'daytime' => 0,
            'daytime13' => 2000,
            'daytime14' => 2335,
            'daytime15' => 2500,
            'night' => 0,
            'evening' => 0,
            'day' => 3500,
            'arbitrary' => 0,
        ];

        if (isset($titles[$this->type])) {
            return $titles[$this->type];
        }
        return 0;
    }

    public function getTransportReport(): ?AdministratorReport
    {
        return $this->transportReport;
    }

    public function setTransportReport(?AdministratorReport $transportReport): static
    {
        $this->transportReport = $transportReport;

        return $this;
    }

    private function getTypePrice(): int
    {
        $titles = [
            'daytime' => 0,
            'daytime13' => 2000,
            'daytime14' => 2350,
            'daytime15' => 2500,
            'night' => 0,
            'evening' => 0,
            'day' => 4000,
            'arbitrary' => 0,
        ];

        if (isset($titles[$this->type])) {
            return $titles[$this->type];
        }
        return 0;
    }

    public function getCallType(): ?string
    {
        return $this->callType;
    }

    public function setCallType(?string $callType): static
    {
        $this->callType = $callType;

        return $this;
    }
}
