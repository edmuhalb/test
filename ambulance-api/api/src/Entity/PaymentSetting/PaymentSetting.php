<?php

declare(strict_types=1);

namespace App\Entity\PaymentSetting;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Repository\PaymentSetting\PaymentSettingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PaymentSettingRepository::class)]
#[ORM\Table(name: 'payment_setting')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Put(),
        new Patch(),
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['payment-setting:read']],
    denormalizationContext: ['groups' => ['payment-setting:write']],
    openapi: false,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
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
class PaymentSetting
{
    /** Процент оператору за терапию */
    public const OPERATOR_PERCENT_THERAPY = 'operator_percent_therapy';
    /** Процент оператору за госпитализацию */
    public const OPERATOR_PERCENT_HOSPITAL = 'operator_percent_hospital';
    /** Процент оператору за кодировку */
    public const OPERATOR_PERCENT_CODING = 'operator_percent_coding';
    /** Награда оператору за стационар */
    public const OPERATOR_REWARD_STATIONARY = 'operator_reward_stationary';

    #[ORM\Id]
    #[ORM\Column]
    #[Groups(['payment-setting:read'])]
    private string $id;

    #[ORM\Column]
    #[Groups(['payment-setting:read', 'payment-setting:write'])]
    private int $value;

    #[ORM\Column(length: 255)]
    #[Groups(['payment-setting:read'])]
    private string $title;

    #[ORM\OneToMany(
        mappedBy: 'paymentSetting',
        targetEntity: PaymentSettingVersion::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $versions;

    public function __construct(
        string $id,
        int $value,
        string $title
    ) {
        $this->id = $id;
        $this->value = $value;
        $this->title = $title;
        $this->versions = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Collection<int, PaymentSettingVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(PaymentSettingVersion $version): self
    {
        $this->versions->add($version);

        return $this;
    }
}
