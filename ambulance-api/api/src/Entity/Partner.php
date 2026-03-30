<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Partner\CreatePartner;
use App\Entity\Calling\Calling;
use App\Entity\Partner\Agreement\Agreement;
use App\Filter\Partner\PartnerCallingCityFilter;
use App\Filter\Partner\PartnerCallingCompletedAtFilter;
use App\Filter\Partner\SearchByFieldsFilter;
use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/exchange/partners',
            normalizationContext: [
                'groups' => [
                    'exchange_partners:read',
                ],
            ]
        ),
        new GetCollection(
            routePrefix: '/api',
            openapi: false,
        ),
        new Post(
            routePrefix: '/api',
            openapi: false,
        ),
        new Post(
            uriTemplate: '/exchange/partners',
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
            controller: CreatePartner::class,
        ),
        new Get(
            routePrefix: '/api',
            openapi: false,
        ),
        new Put(
            routePrefix: '/api',
            openapi: false,
        ),
    ],
    normalizationContext: ['groups' => ['partner:read', 'partner:item:read']],
    denormalizationContext: ['groups' => ['partner:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(PartnerCallingCompletedAtFilter::class, properties: ['name' => 'completedAt'])]
#[ApiFilter(PartnerCallingCityFilter::class, properties: ['name' => 'city.id'])]
#[ApiFilter(
    SearchByFieldsFilter::class,
    properties: ['name']
)]
#[ApiFilter(
    SearchFilter::class,
    properties: ['externalId' => 'exact']
)]
class Partner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['partner:item:read', 'partner_user:read', 'exchange_partners:read', 'v1-call:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['partner:item:read', 'partner:write', 'partner_user:read', 'exchange_partners:read', 'v1-call:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['partner:read', 'partner:write', 'exchange_partners:read'])]
    private ?string $externalId = null;

    #[ORM\OneToMany(mappedBy: 'partner', targetEntity: Calling::class)]
    private Collection $callings;

    #[ORM\OneToMany(mappedBy: 'partner', targetEntity: Agreement::class)]
    #[Groups(['exchange_partners:read'])]
    private Collection $agreements;

    #[ORM\Column(length: 11, nullable: true)]
    #[Groups(['partner:read', 'partner:write', 'exchange_partners:read'])]
    #[Assert\Regex(
        pattern: '/\d{11}/',
        message: 'Номер телефона должен состоять из 11 цифр.'
    )]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['partner:read', 'partner:write', 'exchange_partners:read'])]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['partner:read', 'partner:write'])]
    private ?string $fullName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['partner:read', 'partner:write'])]
    private ?string $contactPerson = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['partner:item:read', 'partner:write', 'exchange_partners:read'])]
    private ?string $whatsappGroup = null;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['partner:read', 'partner:write', 'exchange_partners:read'])]
    #[ApiFilter(BooleanFilter::class)]
    private bool $noBusinessCards;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['partner:read', 'partner:write', 'exchange_partners:read'])]
    #[ApiFilter(BooleanFilter::class)]
    private bool $partnerHospitalization;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['partner:read', 'partner:write', 'exchange_partners:read'])]
    #[ApiFilter(BooleanFilter::class)]
    private bool $our;

    public function __construct()
    {
        $this->callings = new ArrayCollection();
        $this->agreements = new ArrayCollection();
        $this->noBusinessCards = false;
        $this->partnerHospitalization = false;
        $this->our = false;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
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
            $calling->setPartner($this);
        }

        return $this;
    }

    public function removeCalling(Calling $calling): self
    {
        if ($this->callings->removeElement($calling)) {
            // set the owning side to null (unless already changed)
            if ($calling->getPartner() === $this) {
                $calling->setPartner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Agreement>
     */
    public function getAgreements(): Collection
    {
        return $this->agreements;
    }

    public function addAgreement(Agreement $agreement): self
    {
        if (!$this->agreements->contains($agreement)) {
            $this->agreements->add($agreement);
            $agreement->setPartner($this);
        }

        return $this;
    }

    public function removeAgreement(Agreement $agreement): self
    {
        if ($this->agreements->removeElement($agreement)) {
            // set the owning side to null (unless already changed)
            if ($agreement->getPartner() === $this) {
                $agreement->setPartner(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function setContactPerson(?string $contactPerson): self
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    public function getWhatsappGroup(): ?string
    {
        return $this->whatsappGroup;
    }

    public function setWhatsappGroup(?string $whatsappGroup): self
    {
        $this->whatsappGroup = $whatsappGroup;

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

    public function isPartnerHospitalization(): bool
    {
        return $this->partnerHospitalization;
    }

    public function setPartnerHospitalization(bool $partnerHospitalization): self
    {
        $this->partnerHospitalization = $partnerHospitalization;

        return $this;
    }

    public function isOur(): bool
    {
        return $this->our;
    }

    public function setOur(bool $our): self
    {
        $this->our = $our;

        return $this;
    }
}
