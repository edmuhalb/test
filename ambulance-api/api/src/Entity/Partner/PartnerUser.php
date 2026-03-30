<?php

declare(strict_types=1);

namespace App\Entity\Partner;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Partner;
use App\Repository\PartnerUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PartnerUserRepository::class)]
#[ORM\Table(name: 'partner_users')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Put(),
        new Patch(),
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['partner_user:read']],
    denormalizationContext: ['groups' => ['partner_user:write']],
    openapi: false,
)]
#[UniqueEntity(fields: ['phone'], message: 'Этот номер телефона уже используется.')]
class PartnerUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_PARTNER_USER = 'ROLE_PARTNER_USER';
    public const ROLE_PARTNER_OWNER = 'ROLE_PARTNER_OWNER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'partner_user:read',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 11, unique: true)]
    #[Groups([
        'partner_user:read',
        'partner_user:write',
    ])]
    #[Assert\NotBlank(message: 'Телефон обязателен для заполнения.')]
    #[Assert\Regex(
        pattern: '/\d{11}/',
        message: 'Номер телефона должен состоять из 11 цифр.'
    )]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups([
        'partner_user:read',
        'partner_user:write',
    ])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups([
        'partner_user:read',
        'partner_user:write',
    ])]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\Length(min: 4)]
    #[Groups([
        'partner_user:write',
    ])]
    #[SerializedName('password')]
    private ?string $plainPassword = null;

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    #[Groups([
        'partner_user:read',
        'partner_user:write',
    ])]
    private ?Partner $partner = null;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->phone;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        /** @var array<array-key, string> $roles */
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
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
}
