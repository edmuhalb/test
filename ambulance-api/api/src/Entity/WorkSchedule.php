<?php

declare(strict_types=1);

namespace App\Entity;

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
use App\Entity\User\User;
use App\Repository\WorkScheduleRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkScheduleRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Put(),
        new Delete(),
        new Patch(),
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['work-schedule:read']],
    denormalizationContext: ['groups' => ['work-schedule:write']],
    openapi: false,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'workDate' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'employee.name',
        'workDate',
    ],
    arguments: ['orderParameterName' => 'order']
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'type' => 'exact',
        'employee.id' => 'exact',
        'role' => 'exact',
    ]
)]
class WorkSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['work-schedule:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['work-schedule:read', 'work-schedule:write'])]
    #[Assert\NotBlank]
    private ?DateTimeImmutable $workDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['work-schedule:read', 'work-schedule:write'])]
    #[Assert\NotBlank]
    private ?User $employee = null;

    #[ORM\Column(length: 32)]
    #[Groups(['work-schedule:read', 'work-schedule:write'])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [
        'daytime', // дневная *
        'night',   // ночная
        'evening', // вечерняя
        'day',     // суточная
        'stop',     // выходной
        'reserve',     // резерв
    ])]
    private ?string $type = null;

    #[ORM\Column(length: 64)]
    #[Groups(['work-schedule:read', 'work-schedule:write'])]
    #[Assert\NotBlank]
    private ?string $role = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, columnDefinition: 'INT DEFAULT 1')]
    #[Groups(['work-schedule:read', 'work-schedule:write'])]
    private ?City $city = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkDate(): ?DateTimeImmutable
    {
        return $this->workDate;
    }

    public function setWorkDate(DateTimeImmutable $workDate): self
    {
        $this->workDate = $workDate;

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getDay(): int
    {
        return (int)$this->workDate->format('d');
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
}
