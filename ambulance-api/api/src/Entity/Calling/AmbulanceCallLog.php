<?php

namespace App\Entity\Calling;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\MedTeam\MedTeam;
use App\Entity\ReasonForCancellation;
use App\Entity\User\User;
use App\Repository\Calling\AmbulanceCallLogRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: AmbulanceCallLogRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            routePrefix: '/api/v1',
            shortName: 'AmbulanceCallLog',
            normalizationContext: [
                'groups' => [
                    'ambulance_call_log:read',
                ]],
        ),
        ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'shift.id' => 'exact',
        'ambulanceCall.id' => 'exact',
        'newStatus.id' => 'exact',
        'oldStatus.id' => 'exact',
        'reasonForCancellation.id' => 'exact',
    ]
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'changedAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['changedAt'],
    arguments: ['orderParameterName' => 'order']
)]
class AmbulanceCallLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ambulance_call_log:read',])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ambulanceCallLogs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ambulance_call_log:read',])]
    private Calling $ambulanceCall;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['ambulance_call_log:read',])]
    private ?string $oldStatus;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['ambulance_call_log:read',])]
    private ?string $newStatus;

    #[ORM\ManyToOne]
    #[Groups(['ambulance_call_log:read',])]
    private ?MedTeam $shift;

    #[ORM\ManyToOne]
    #[Groups(['ambulance_call_log:read',])]
    private ?ReasonForCancellation $reasonForCancellation;

    #[ORM\Column]
    #[Groups(['ambulance_call_log:read',])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:i:s'])]
    private DateTimeImmutable $changedAt;

    #[ORM\ManyToOne]
    #[Groups(['ambulance_call_log:read',])]
    private ?User $user;

    public function __construct(
        Calling $ambulanceCall,
        DateTimeImmutable $changedAt,
        ?User $user,
        ?Status $oldStatus,
        ?Status $newStatus,
        ?MedTeam $shift,
        ?ReasonForCancellation $reasonForCancellation,
    )
    {
        $this->ambulanceCall = $ambulanceCall;
        $this->oldStatus = $oldStatus?->getName();
        $this->newStatus = $newStatus?->getName();
        $this->shift = $shift;
        $this->reasonForCancellation = $reasonForCancellation;
        $this->changedAt = $changedAt;
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmbulanceCall(): ?Calling
    {
        return $this->ambulanceCall;
    }

    public function getOldStatus(): ?string
    {
        return $this->oldStatus;
    }

    public function getNewStatus(): ?string
    {
        return $this->newStatus;
    }

    public function getShift(): ?MedTeam
    {
        return $this->shift;
    }

    public function getReasonForCancellation(): ?ReasonForCancellation
    {
        return $this->reasonForCancellation;
    }

    public function getChangedAt(): DateTimeImmutable
    {
        return $this->changedAt;
    }

    #[ApiProperty(
        description: 'Причина отмены',
    )]
    #[Groups(['ambulance_call_log:read',])]
    public function getRejectedComment(): ?string
    {
        return $this->reasonForCancellation?->getName();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
