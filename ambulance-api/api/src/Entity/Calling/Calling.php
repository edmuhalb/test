<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Calling\AcceptAction;
use App\Controller\Calling\ArriveAction;
use App\Controller\Calling\CoddingAction;
use App\Controller\Calling\CompleteAction;
use App\Controller\Calling\CurrentAction;
use App\Controller\Calling\DispatchAction;
use App\Controller\Calling\ExchangeCreateAction;
use App\Controller\Calling\FinishAction;
use App\Controller\Calling\HospitalizationAction;
use App\Controller\Calling\HospitalizationWithoutTherapyAction;
use App\Controller\Calling\HospitalizationWithTherapyAction;
use App\Controller\Calling\NotReadyAction;
use App\Controller\Calling\PatchAction;
use App\Controller\Calling\RecalculateOperatorReward;
use App\Controller\Calling\RejectAction;
use App\Controller\Calling\RepeatAction;
use App\Controller\Calling\StartTreatmentAction;
use App\Entity\CallType;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\MediaObject;
use App\Entity\MedTeam\MedTeam;
use App\Entity\Partner;
use App\Entity\ReasonForCancellation;
use App\Entity\User\User;
use App\Filter\Call\EmployeeFilter;
use App\Filter\Call\SearchByFieldsFilter;
use App\Repository\CallingRepository;
use App\State\Call\PostProcessor;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: CallingRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            routePrefix: '/api/v1',
            shortName: 'AmbulanceCall',
            normalizationContext: [
                'groups' => [
                    'v1-call:item:read',
                ]],
        ),
        new Get(
            routePrefix: '/api/v1',
            shortName: 'AmbulanceCall',
            normalizationContext: [
                'groups' => [
                    'v1-call:read',
                    'client:item:read',
                ]],
        ),
        new Patch(
            routePrefix: '/api/v1',
            controller: PatchAction::class,
            shortName: 'AmbulanceCall',
            normalizationContext: [
                'groups' => [
                    'v1-call:read',
                    'client:item:read',
                ]],
            denormalizationContext: [
                'groups' => [
                    'v1-call:write',
                    'calling:write',
                ],
            ],
            write: false,
        ),

        new GetCollection(
            uriTemplate: '/calls',
            routePrefix: '/api',
            openapi: false,
        ),
        new GetCollection(
            uriTemplate: '/exchange/calls',
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
            normalizationContext: [
                'groups' => [
                    'exchange_calling:read',
                    'partner:item:read',
                    'user:item:read',
                    'service:item:read',
                    'client:item:read',
                ],
            ],
        ),
        new Get(
            uriTemplate: '/exchange/calls/{id}',
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
            normalizationContext: [
                'groups' => [
                    'v1-call:read',
                    'client:item:read',
                    'exchange_calling:read',
                    'partner:item:read',
                    'user:item:read',
                    'service:item:read',
                ]],
        ),
        new Post(
            uriTemplate: '/exchange/calls',
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
            controller: ExchangeCreateAction::class,
            normalizationContext: [
                'groups' => [
                    'exchange_calling:read',
                    'partner:item:read',
                    'user:item:read',
                    'service:item:read',
                    'client:item:read',
                ],
            ],
            denormalizationContext: [
                'groups' => [
                    'exchange_calling:white',
                ]
            ],
            input: ExchangeCallCreateDto::class,
        ),
        new Post(
            routePrefix: '/api',
            openapi: false,
            normalizationContext: [
                'groups' => [
                    'calling:read',
                    'calling:item:read',
                    'calling:detail:read',
                    'partner:item:read',
                    'service:item:read',
                ],
            ],
        ),
        new Get(
            routePrefix: '/api',
            openapi: false,
            normalizationContext: [
                'groups' => [
                    'calling:read',
                    'calling:item:read',
                    'calling:detail:read',
                    'partner:item:read',
                    'service:item:read',
                    'media_object:read',
                ],
            ],
        ),
        new Put(
            routePrefix: '/api',
            openapi: false,
            normalizationContext: [
                'groups' => [
                    'calling:read',
                    'calling:item:read',
                    'calling:detail:read',
                    'partner:item:read',
                    'service:item:read',
                    'media_object:read',
                ],
            ],
            processor: PostProcessor::class
        ),
    ],
    normalizationContext: ['groups' => ['calling:read', 'partner:item:read', 'service:item:read', 'user:item:read']],
    denormalizationContext: ['groups' => ['calling:write', 'media_object:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[Post(
    uriTemplate: '/callings/recalculate-operators-rewards',
    routePrefix: '/api',
    controller: RecalculateOperatorReward::class,
    openapi: false,
)]
#[Post(
    uriTemplate: '/callings/current',
    routePrefix: '/api',
    controller: CurrentAction::class,
    openapi: false,
    input: CallingDto::class,
    read: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/accept',
    routePrefix: '/api',
    controller: AcceptAction::class,
    openapi: false,
    normalizationContext: [
        'groups' => [
            // 'calling:read',
            // 'calling:item:read',
            // 'calling:detail:read',
            // 'partner:item:read',
            // 'service:item:read',
            'media_object:read',
        ],
    ],
    input: CallingDto::class,
    read: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/dispatch',
    routePrefix: '/api',
    controller: DispatchAction::class,
    openapi: false,
    input: CallingDispatchDto::class,
    read: false,
)
]
#[Post(
    uriTemplate: '/callings/{id}/start-treatment',
    routePrefix: '/api',
    controller: StartTreatmentAction::class,
    openapi: false,
    input: CallingArriveDto::class,
    read: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/arrive',
    routePrefix: '/api',
    controller: ArriveAction::class,
    openapi: false,
    input: CallingArriveDto::class,
    read: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/complete',
    routePrefix: '/api',
    controller: CompleteAction::class,
    openapi: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/finish',
    routePrefix: '/api',
    controller: FinishAction::class,
    openapi: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/codding',
    routePrefix: '/api',
    controller: CoddingAction::class,
    openapi: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/hospitalization',
    routePrefix: '/api',
    controller: HospitalizationAction::class,
    openapi: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/hospitalization-with-therapy',
    routePrefix: '/api',
    controller: HospitalizationWithTherapyAction::class,
    openapi: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/hospitalization-without-therapy',
    routePrefix: '/api',
    controller: HospitalizationWithoutTherapyAction::class,
    openapi: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/repeat',
    routePrefix: '/api',
    controller: RepeatAction::class,
    openapi: false,
)]
#[Post(
    uriTemplate: '/callings/{id}/reject',
    routePrefix: '/api',
    controller: RejectAction::class,
    openapi: false,
)]
#[Post(
    uriTemplate: '/calls/{id}/not-ready',
    routePrefix: '/api',
    controller: NotReadyAction::class,
    openapi: false,
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['createdAt', 'updatedAt', 'completedAt', 'dateTime'],
    arguments: ['orderParameterName' => 'order']
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'dateTime' => DateFilterInterface::EXCLUDE_NULL,
        'createdAt' => DateFilterInterface::EXCLUDE_NULL,
        'updatedAt' => DateFilterInterface::EXCLUDE_NULL,
        'completedAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
#[ApiFilter(
    EmployeeFilter::class,
    properties: ['employee']
)]
#[ApiFilter(
    SearchByFieldsFilter::class,
    properties: ['search']
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'team.id' => 'exact',
        'services.service.id' => 'exact',
        'type' => 'exact',
    ]
)]
class Calling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['calling:read', 'hospital:detail:read', 'exchange_calling:read', 'v1-call:read', 'v1-call:item:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 256)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read', 'v1-call:item:read'])]
    private string $title;

    #[ORM\Column(length: 128)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read', 'v1-call:item:read'])]
    private string $name;

    #[ORM\Column(length: 16)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read', 'v1-call:item:read'])]
    private string $phone;

    #[ApiProperty(
        description: 'ФИО пациента',
    )]
    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read', 'v1-call:write'])]
    private ?string $fio = null;

    #[ORM\Column(length: 32)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    private string $numberCalling;

    #[ORM\Column(length: 255)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read', 'v1-call:item:read'])]
    private string $address;

    #[ORM\Column(type: 'calling_status', length: 16, nullable: false)]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    private Status $status;

    #[ApiProperty(
        description: 'Описание',
    )]
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    private string $description;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    private ?string $chronicDiseases = null;

    #[ApiProperty(
        description: 'Нозология',
    )]
    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    private ?string $nosology = null;

    #[ApiProperty(
        description: 'Возраст пациента',
    )]
    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    private ?string $age = null;
    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    private ?string $leadType = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?string $partnerName = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['calling:read', 'calling:write'])]
    #[ApiFilter(BooleanFilter::class)]
    private bool $sendPhone = false;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read', 'v1-call:write'])]
    private ?string $rejectedComment = null;

    #[ORM\Column]
    #[Groups(['calling:read', 'exchange_calling:read', 'v1-call:item:read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['calling:read', 'exchange_calling:read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'exchange_calling:read'])]
    private ?DateTimeImmutable $acceptedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'exchange_calling:read'])]
    private ?DateTimeImmutable $dispatchedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'exchange_calling:read'])]
    private ?DateTimeImmutable $arrivedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'exchange_calling:read'])]
    private ?DateTimeImmutable $completedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'exchange_calling:read', 'v1-call:read', 'v1-call:item:read'])]
    private ?DateTimeImmutable $dateTime = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['calling:read', 'exchange_calling:read', 'v1-call:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['admin.id' => 'exact'])]
    private ?User $admin;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['calling:read', 'exchange_calling:read', 'v1-call:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['doctor.id' => 'exact'])]
    private ?User $doctor;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?int $estimated = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?int $prepayment = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read', 'v1-call:write', 'exchange_calling:read' ])]
    private ?string $note = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?string $passport = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?int $coastHospitalAdmission = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?int $coastHospital = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?int $costDay = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $phoneRelatives = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?string $resultDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?string $resultTime = null;

    #[ORM\ManyToOne(inversedBy: 'callings')]
    #[Groups(['calling:read', 'exchange_calling:read', 'v1-call:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['partner.id' => 'exact'])]
    private ?Partner $partner = null;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $deleted;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?string $lon;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?string $lat;

    #[ORM\OneToMany(mappedBy: 'calling', targetEntity: Row::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:write'])]
    private Collection $services;

    #[ORM\Column(nullable: true)]
    private ?int $amount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?int $paymentNextOrder = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $paymentHospitalization = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    private ?int $totalAmount = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $owner = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read'])]
    private ?int $partnerReward = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read'])]
    private ?int $mkadDistance = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read'])]
    private ?string $ownerExternalId = null;

    #[ORM\ManyToOne]
    #[Groups(['calling:read', 'exchange_calling:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['operator.id' => 'exact'])]
    private ?User $operator = null;

    #[ORM\Embedded(class: OperatorReward::class)]
    #[Groups(['calling:read', 'exchange_calling:read'])]
    private OperatorReward $operatorReward;

    #[ORM\ManyToOne(inversedBy: 'callings')]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['client.id' => 'exact'])]
    private ?Client $client = null;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['calling:read', 'calling:write'])]
    #[ApiFilter(BooleanFilter::class)]
    private bool $noBusinessCards;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['calling:read', 'calling:write'])]
    #[ApiFilter(BooleanFilter::class)]
    private bool $partnerHospitalization;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    #[ApiFilter(BooleanFilter::class)]
    private bool $personal;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read'])]
    #[ApiFilter(BooleanFilter::class)]
    private bool $doNotHospitalize;

    #[ORM\ManyToMany(targetEntity: MediaObject::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'calling_images')]
    #[ORM\JoinColumn(name: 'call_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'media_object_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['calling:read', 'calling:write', 'hospital:detail:read', 'v1-call:read'])]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read'])]
    private ?string $addressInfo = null;

    #[ORM\ManyToOne(inversedBy: 'callings')]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read'])]
    private ?MedTeam $team = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read'])]
    private ?int $responsibleUserId = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read'])]
    private ?string $responsibleUserName = null;

    #[ORM\ManyToOne]
    #[Groups(['calling:read', 'calling:write'])]
    #[ApiFilter(SearchFilter::class, properties: ['city.id' => 'exact'])]
    private ?City $city = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read', 'v1-call:write'])]
    private ?DateTimeImmutable $arrivalDateTime = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'v1-call:read', 'v1-call:write'])]
    private ?DateTimeImmutable $endOfServiceDateTime = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['v1-call:read', 'v1-call:write', 'exchange_calling:read'])]
    private ?DateTimeImmutable $birthday = null;

    #[ORM\ManyToOne]
    #[Groups(['v1-call:read', 'v1-call:write'])]
    private ?ReasonForCancellation $reasonForCancellation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentForPartner = null;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private ?bool $isBuh = false;

    /**
     * @var Collection<int, AmbulanceCallLog>
     */
    #[ORM\OneToMany(mappedBy: 'ambulanceCall', targetEntity: AmbulanceCallLog::class, orphanRemoval: true)]
    private Collection $ambulanceCallLogs;

    #[ORM\Column(length: 255, options: ['default' => CallType::NARCOLOGY])]
    #[Groups(['calling:read', 'calling:write', 'exchange_calling:read', 'v1-call:read', 'v1-call:item:read'])]
    private ?string $type;

    public function __construct(
        string  $numberCalling,
        string  $title,
        string  $name,
        string  $phone,
        ?string $address = null,
        ?string $description = null,
        ?User   $admin = null,
        ?User   $doctor = null
    )
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->address = $address ?: '';
        $this->description = $description ?: '';
        $this->status = Status::assigned();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->numberCalling = $numberCalling;
        $this->title = $title;
        $this->admin = $admin;
        $this->doctor = $doctor;
        $this->deleted = false;
        $this->lat = null;
        $this->lon = null;
        $this->services = new ArrayCollection();
        $this->operatorReward = new OperatorReward(0, 0, 0, 0);
        $this->noBusinessCards = false;
        $this->partnerHospitalization = false;
        $this->personal = false;
        $this->doNotHospitalize = false;

        $this->images = new ArrayCollection();
        $this->ambulanceCallLogs = new ArrayCollection();

        $this->type = CallType::NARCOLOGY;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address ?: '';

        return $this;
    }

    public function setAccepted(DateTimeImmutable $acceptedAt): void
    {
        $this->status = Status::accepted();
        $this->acceptedAt = $acceptedAt;
    }

    public function setArrived(DateTimeImmutable $arrivedAt): void
    {
        $this->status = Status::arrived();
        $this->arrivedAt = $arrivedAt;
    }

    public function setDispatched(DateTimeImmutable $dispatchedAt): void
    {
        $this->status = Status::dispatched();
        $this->dispatchedAt = $dispatchedAt;
    }

    public function setComplete(DateTimeImmutable $completedAt): void
    {
        $this->status = Status::completed();
        $this->completedAt = $completedAt;
    }

    public function setReject(DateTimeImmutable $completedAt, ?string $comment): void
    {
        $this->status = Status::rejected();
        $this->completedAt = $completedAt;
        $this->rejectedComment = $comment;
    }

    public function getStatus(): string
    {
        return $this->status->getName();
    }

    public function getDescription(): ?string
    {
        if (
            $this->status === Status::notReady() ||
            $this->status === Status::assigned() ||
            $this->status === Status::accepted() ||
            $this->status === Status::dispatched()) {

            return '';
        }
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAcceptedAt(): ?DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    #[ApiProperty(
        description: 'Причина отмены',
    )]
    #[Groups(['exchange_calling:read',])]
    public function getRejectedComment(): ?string
    {
        if ($this->reasonForCancellation) {
            return $this->reasonForCancellation->getName();
        }

        return $this->rejectedComment;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getOriginalPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function inProgress(): bool
    {
        return !$this->status->isRejected() && !$this->status->isCompleted();
    }

    public function getArrivedAt(): ?DateTimeImmutable
    {
        return $this->arrivedAt;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getNumberCalling(): string
    {
        return $this->numberCalling;
    }

    public function getChronicDiseases(): ?string
    {
        return $this->chronicDiseases;
    }

    public function getLeadType(): ?string
    {
        return $this->leadType;
    }

    public function getPartnerName(): ?string
    {
        return $this->partnerName;
    }

    public function isSendPhone(): bool
    {
        return $this->sendPhone;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getDateTime(): ?DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setNumberCalling(string $numberCalling): void
    {
        $this->numberCalling = $numberCalling;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    #[Groups(['v1-call:write'])]
    #[SerializedName('status')]
    public function setStatusValue(string $value): void
    {
        $this->status = new Status($value);
    }

    #[Groups(['calling:read', 'exchange_calling:read', 'v1-call:read', 'v1-call:item:read'])]
    #[SerializedName('status')]
    public function getStatusValue(): string
    {
        return $this->status->getName();
    }

    public function setChronicDiseases(?string $chronicDiseases): void
    {
        $this->chronicDiseases = $chronicDiseases;
    }

    public function setLeadType(?string $leadType): void
    {
        $this->leadType = $leadType;
    }

    public function setPartnerName(?string $partnerName): void
    {
        $this->partnerName = $partnerName;
    }

    public function setSendPhone(bool $sendPhone): void
    {
        $this->sendPhone = $sendPhone;
    }

    public function setRejectedComment(?string $rejectedComment): void
    {
        $this->rejectedComment = $rejectedComment;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setAcceptedAt(?DateTimeImmutable $acceptedAt): void
    {
        $this->acceptedAt = $acceptedAt;
    }

    public function setArrivedAt(?DateTimeImmutable $arrivedAt): void
    {
        $this->arrivedAt = $arrivedAt;
    }

    public function setCompletedAt(?DateTimeImmutable $completedAt): void
    {
        $this->completedAt = $completedAt;
    }

    public function setDateTime(?DateTimeImmutable $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getNosology(): ?string
    {
        return $this->nosology;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setNosology(?string $nosology): void
    {
        $this->nosology = $nosology;
    }

    public function setAge(?string $age): void
    {
        $this->age = $age;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getEstimated(): ?int
    {
        return $this->estimated;
    }

    public function setEstimated(?int $estimated): self
    {
        $this->estimated = $estimated;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getPassport(): ?string
    {
        return $this->passport;
    }

    public function setPassport(?string $passport): self
    {
        $this->passport = $passport;

        return $this;
    }

    public function getCoastHospital(): ?int
    {
        return $this->coastHospital;
    }

    public function setCoastHospital(?int $coastHospital): self
    {
        $this->coastHospital = $coastHospital;

        return $this;
    }

    public function getCostDay(): ?int
    {
        return $this->costDay;
    }

    public function setCostDay(?int $costDay): self
    {
        $this->costDay = $costDay;

        return $this;
    }

    public function getPhoneRelatives(): ?string
    {
        return $this->phoneRelatives;
    }

    public function setPhoneRelatives(?string $phoneRelatives): self
    {
        $this->phoneRelatives = $phoneRelatives;

        return $this;
    }

    public function getResultDate(): ?string
    {
        return $this->resultDate;
    }

    /**
     * @throws Exception
     */
    public function getResultDateFormat(): ?string
    {
        return $this->resultDate ? (new DateTimeImmutable($this->resultDate))->format('d.m.y H:m') : null;
    }

    public function setResultDate(?string $resultDate): self
    {
        $this->resultDate = $resultDate;

        return $this;
    }

    public function getResultTime(): ?string
    {
        return $this->resultTime;
    }

    public function setResultTime(?string $resultTime): self
    {
        $this->resultTime = $resultTime;

        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
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

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getLon(): ?string
    {
        return $this->lon;
    }

    public function setLon(?string $lon): void
    {
        $this->lon = $lon;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): void
    {
        $this->lat = $lat;
    }

    public function getCoastHospitalAdmission(): ?int
    {
        return $this->coastHospitalAdmission;
    }

    public function setCoastHospitalAdmission(?int $coastHospitalAdmission): void
    {
        $this->coastHospitalAdmission = $coastHospitalAdmission;
    }

    public function getFio(): ?string
    {
        return $this->fio;
    }

    public function setFio(?string $fio): void
    {
        $this->fio = $fio;
    }

    public function getDispatchedAt(): ?DateTimeImmutable
    {
        return $this->dispatchedAt;
    }

    /**
     * @return Collection<int, Row>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    #[Groups(['v1-call:read'])]
    #[SerializedName('services')]
    public function getServicesValues(): array
    {
        return $this->services->getValues();
    }

    public function addService(Row $row): self
    {
        if (!$this->services->contains($row)) {
            $this->services->add($row);
            $row->setCalling($this);
        }

        return $this;
        // if (!$this->services->contains($row)) {
        //     $this->services->add($row);
        //     $row->setCalling($this);
        // }
        //
        // $this->price = 0;
        // $this->paymentNextOrder = 0;
        // $this->totalAmount = 0;
        //
        // /** @var Row $serviceRow */
        // foreach ($this->services as $serviceRow) {
        //     if ($serviceRow->getService()->getType() === 'default') {
        //         $this->price += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
        //     } else {
        //         $this->paymentNextOrder += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
        //     }
        //     $this->totalAmount = $this->price + $this->paymentNextOrder;
        // }
        //
        // return $this;
    }

    public function removeService(Row $row): self
    {
        if ($this->services->removeElement($row)) {
            // set the owning side to null (unless already changed)
            if ($row->getCalling() === $this) {
                $row->setCalling(null);
            }
        }

        return $this;
        // if ($this->services->removeElement($row)) {
        //     // set the owning side to null (unless already changed)
        //     if ($row->getCalling() === $this) {
        //         $row->setCalling(null);
        //     }
        // }
        //
        // $this->price = 0;
        // $this->paymentNextOrder = 0;
        // $this->totalAmount = 0;
        //
        // /** @var Row $serviceRow */
        // foreach ($this->services as $serviceRow) {
        //     if ($serviceRow->getService()->getType() === 'default') {
        //         $this->price += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
        //     } else {
        //         $this->paymentNextOrder += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
        //     }
        //     $this->totalAmount = $this->price + $this->paymentNextOrder;
        // }
        //
        // return $this;
    }

    // public function setServices($services): self
    // {
    //     $this->services = new ArrayCollection($services);
    //     return $this;
    // }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentNextOrder(): ?int
    {
        return $this->paymentNextOrder;
    }

    public function setPaymentNextOrder(?int $paymentNextOrder): self
    {
        $this->paymentNextOrder = $paymentNextOrder;

        return $this;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?int $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getOwner(): ?self
    {
        return $this->owner;
    }

    public function setOwner(?self $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getPartnerReward(): ?int
    {
        return $this->partnerReward;
    }

    public function setPartnerReward(?int $partnerReward): self
    {
        $this->partnerReward = $partnerReward;

        return $this;
    }

    public function getMkadDistance(): ?int
    {
        return $this->mkadDistance;
    }

    public function setMkadDistance(?int $mkadDistance): self
    {
        $this->mkadDistance = $mkadDistance;

        return $this;
    }

    public function isComplete(): bool
    {
        return $this->status === Status::completed();
    }

    #[SerializedName('repeat')]
    #[Groups(['calling:read', 'exchange_calling:read'])]
    public function getCountRepeat(): int
    {
        if ($this->owner) {
            return $this->owner->getCountRepeat() + 1;
        }

        return 0;
    }

    public function getPaymentHospitalization(): ?int
    {
        return $this->paymentHospitalization;
    }

    public function setPaymentHospitalization(?int $paymentHospitalization): void
    {
        $this->paymentHospitalization = $paymentHospitalization;
    }

    public function getOwnerExternalId(): ?string
    {
        return $this->ownerExternalId;
    }

    public function setOwnerExternalId(?string $ownerExternalId): void
    {
        $this->ownerExternalId = $ownerExternalId;
    }

    public function getOperator(): ?User
    {
        return $this->operator;
    }

    public function setOperator(?User $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    public function getOperatorReward(): OperatorReward
    {
        return $this->operatorReward;
    }

    public function setOperatorReward(OperatorReward $operatorReward): void
    {
        $this->operatorReward = $operatorReward;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function isNoBusinessCards(): bool
    {
        return $this->noBusinessCards;
    }

    public function setNoBusinessCards(bool $noBusinessCards): self
    {
        $this->noBusinessCards = $noBusinessCards;

        return $this;
    }

    #[Groups(['calling:read', 'v1-call:read'])]
    public function isCurrentNoBusinessCards(): bool
    {
        return $this->partner?->isNoBusinessCards() ?: $this->noBusinessCards;
    }

    public function isPartnerHospitalization(): bool
    {
        return $this->partnerHospitalization;
    }

    public function setPartnerHospitalization(bool $partnerHospitalization): self
    {
        $this->partnerHospitalization = $partnerHospitalization;

        return $this;
    }

    #[Groups(['calling:read', 'v1-call:read'])]
    public function isCurrentPartnerHospitalization(): bool
    {
        return $this->partner?->isPartnerHospitalization() ?: $this->partnerHospitalization;
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

    public function getAddressInfo(): ?string
    {
        return $this->addressInfo;
    }

    public function setAddressInfo(?string $addressInfo): self
    {
        $this->addressInfo = $addressInfo;

        return $this;
    }

    public function getTeam(): ?MedTeam
    {
        return $this->team;
    }

    public function setTeam(?MedTeam $team): self
    {
        if ($this->team === $team) {
            return $this;
        }
        $this->team = $team;

        $this->admin = $team?->getAdmin();
        $this->doctor = $team?->getDoctor();

        return $this;
    }

    public function isPersonal(): bool
    {
        return $this->personal;
    }

    public function setPersonal(bool $personal): void
    {
        $this->personal = $personal;
    }

    public function isDoNotHospitalize(): bool
    {
        return $this->doNotHospitalize;
    }

    public function setDoNotHospitalize(bool $doNotHospitalize): void
    {
        $this->doNotHospitalize = $doNotHospitalize;
    }

    public function getResponsibleUserId(): ?int
    {
        return $this->responsibleUserId;
    }

    public function setResponsibleUserId(?int $responsibleUserId): void
    {
        $this->responsibleUserId = $responsibleUserId;
    }

    public function getResponsibleUserName(): ?string
    {
        return $this->responsibleUserName;
    }

    public function setResponsibleUserName(?string $responsibleUserName): void
    {
        $this->responsibleUserName = $responsibleUserName;
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

    public function isAdminCombo(): bool
    {
        if ($this->getCountRepeat() < 2) {
            return false;
        }

        return $this->allOwnerHaveTheSameAdmin();
    }

    public function isDoctorCombo(): bool
    {
        if ($this->getCountRepeat() < 2) {
            return false;
        }

        return $this->allOwnerHaveTheSameDoctor();
    }

    public function getTherapySum(): null|float|int
    {
        $sum = 0;
        foreach ($this->getServices() as $service) {
            $sum += $service->isTherapy() ? $service->getPrice() : 0;
        }
        return $sum;
    }

    #[Groups(['calling:read', 'v1-call:read', 'v1-call:item:read'])]
    public function getStatusLabel(): string
    {
        return $this->status->getLabel();
    }

    public function getArrivalDateTime(): ?DateTimeImmutable
    {
        return $this->arrivalDateTime;
    }

    public function setArrivalDateTime(?DateTimeImmutable $arrivalDateTime): static
    {
        $this->arrivalDateTime = $arrivalDateTime;

        return $this;
    }

    public function getEndOfServiceDateTime(): ?DateTimeImmutable
    {
        return $this->endOfServiceDateTime;
    }

    public function setEndOfServiceDateTime(?DateTimeImmutable $endOfServiceDateTime): static
    {
        $this->endOfServiceDateTime = $endOfServiceDateTime;

        return $this;
    }

    public function getBirthday(): ?DateTimeImmutable
    {
        return $this->birthday;
    }

    public function setBirthday(?DateTimeImmutable $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getReasonForCancellation(): ?ReasonForCancellation
    {
        return $this->reasonForCancellation;
    }

    public function setReasonForCancellation(?ReasonForCancellation $reasonForCancellation): static
    {
        $this->reasonForCancellation = $reasonForCancellation;

        return $this;
    }

    private function allOwnerHaveTheSameAdmin(): bool
    {
        if (!$this->getOwner()) {
            return true;
        }

        if ($this->getOwner()->getAdmin()?->getId() !== $this->getAdmin()?->getId()) {
            return false;
        }

        return $this->getOwner()->allOwnerHaveTheSameAdmin();
    }

    private function allOwnerHaveTheSameDoctor(): bool
    {
        if (!$this->getOwner()) {
            return true;
        }

        if ($this->getOwner()->getDoctor()?->getId() !== $this->getDoctor()?->getId()) {
            return false;
        }

        return $this->getOwner()->allOwnerHaveTheSameDoctor();
    }

    public function getCommentForPartner(): ?string
    {
        return $this->commentForPartner;
    }

    public function setCommentForPartner(?string $commentForPartner): static
    {
        $this->commentForPartner = $commentForPartner;

        return $this;
    }

    public function isBuh(): ?bool
    {
        return $this->isBuh;
    }

    public function setBuh(bool $isBuh): static
    {
        $this->isBuh = $isBuh;

        return $this;
    }

    #[Groups(['v1-call:read'])]
    public function getIsShiftOpen(): bool
    {
        return $this->team?->getStatus() === 'work';
    }

    /**
     * @return Collection<int, AmbulanceCallLog>
     */
    public function getAmbulanceCallLogs(): Collection
    {
        return $this->ambulanceCallLogs;
    }

    public function addAmbulanceCallLog(AmbulanceCallLog $ambulanceCallLog): static
    {
        if (!$this->ambulanceCallLogs->contains($ambulanceCallLog)) {
            $this->ambulanceCallLogs->add($ambulanceCallLog);
            $ambulanceCallLog->setAmbulanceCall($this);
        }

        return $this;
    }

    public function removeAmbulanceCallLog(AmbulanceCallLog $ambulanceCallLog): static
    {
        if ($this->ambulanceCallLogs->removeElement($ambulanceCallLog)) {
            // set the owning side to null (unless already changed)
            if ($ambulanceCallLog->getAmbulanceCall() === $this) {
                $ambulanceCallLog->setAmbulanceCall(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        if ($type === null) {
            $this->type = CallType::NARCOLOGY;
            return $this;
        }

        $this->type = $type;

        return $this;
    }
}
