<?php

declare(strict_types=1);

namespace App\Entity\User;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\User\MyAction;
use App\Entity\City;
use App\Entity\Device;
use App\Entity\Role\Permission;
use App\Entity\Role\Role;
use App\Entity\Team\Team;
use App\Filter\User\SearchByNameAndPhoneAndEmailFilter;
use App\Filter\User\SearchByPermissionsFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(routePrefix: '/api', openapi: true),
        new GetCollection(uriTemplate: '/exchange/users'),
        new Post(routePrefix: '/api', openapi: false),
        new Get(routePrefix: '/api', openapi: false),
        new Put(routePrefix: '/api', openapi: false),
    ],
    normalizationContext: ['groups' => ['user:read', 'city:read']],
    denormalizationContext: ['groups' => ['user:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
)]
#[UniqueEntity(fields: ['phone'], message: 'Этот номер телефона уже используется.')]
#[Post(uriTemplate: '/users/my', routePrefix: '/api', controller: MyAction::class, openapi: false, input: UserDto::class, read: false)]
#[ApiFilter(
    SearchByNameAndPhoneAndEmailFilter::class,
    properties: ['search']
)]
#[ApiFilter(
    SearchByPermissionsFilter::class,
    properties: ['permissions']
)]
#[ApiFilter(SearchFilter::class, properties: ['accessRoles.id' => 'exact', 'cities.id' => 'exact'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'user:read',
        'user:item:read',
        'team:item:get',
        'team:item:get:team_my',
        'calling:detail:read',
        'hospital:read',
        'work-schedule:read',
        'v1:shift:item:read',
        'v1:shift:write',
        'v1-call:read',
        'kpi_document:read',
        'administrator_report:read',
        'ambulance_call_log:read'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 11, unique: true)]
    #[Groups([
        'user:read',
        'user:write',
        'team:item:get',
        'calling:detail:read',
        'med-team:read',
    ])]
    #[Assert\NotBlank(message: 'Телефон обязателен для заполнения.')]
    #[Assert\Regex(
        pattern: '/\d{11}/',
        message: 'Номер телефона должен состоять из 11 цифр.'
    )]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups([
        'user:read',
        'user:item:read',
        'user:write',
        'team:item:get',
        'calling:detail:read',
        'hospital:read',
        'work-schedule:read',
        'v1:shift:item:read',
        'v1-call:read',
        'kpi_document:read',
        'administrator_report:read',
        'ambulance_call_log:read'
    ])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'user:read',
        'user:item:read',
        'user:write',
    ])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'user:read',
        'user:item:read',
        'user:write',
    ])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'user:read',
        'user:item:read',
        'user:write',
    ])]
    private ?string $patronymic = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write', 'team:item:get'])]
    private ?string $position = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write', 'team:item:get'])]
    private ?string $avatar = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\Length(min: 4)]
    #[Groups(['user:write'])]
    #[SerializedName('password')]
    private ?string $plainPassword = null;

    #[ORM\OneToMany(mappedBy: 'administrator', targetEntity: Team::class)]
    private Collection $teams;

    #[ORM\Column(nullable: true)]
    private ?int $externalId;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Device::class)]
    private Collection $devices;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(options: ['default' => 1])]
    #[Groups(['user:read', 'user:item:read', 'user:write'])]
    #[ApiFilter(BooleanFilter::class)]
    private ?bool $active = true;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['user:read', 'user:item:read', 'user:write'])]
    private bool $hideInReports = false;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[Groups(['user:read', 'user:write'])]
    private Collection $accessRoles;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $telegram = null;

    /**
     * @var Collection<int, City>
     */
    #[ORM\ManyToMany(targetEntity: City::class)]
    #[Groups(['user:read', 'user:write'])]
    private Collection $cities;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $carNumber = null;

    public function __construct($externalId = null)
    {
        $this->teams = new ArrayCollection();
        $this->externalId = $externalId;
        $this->devices = new ArrayCollection();
        $this->accessRoles = new ArrayCollection();
        $this->cities = new ArrayCollection();
    }

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
        if (!$this->active) {
            return [];
        }
        /** @var array<array-key, string> $roles */
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
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

    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        $this->teams->add($team);
        $team->setAdministrator($this);

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        $this->teams->removeElement($team);
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->setUser($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        $this->devices->removeElement($device);

        return $this;
    }

    public static function createFromPayload($username, array $payload): self
    {
        return (new self())
            ->setId($username)
            ->setRoles($payload['roles'])
            ->setPhone($payload['phone']);
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function isHideInReports(): ?bool
    {
        return $this->hideInReports;
    }

    public function setHideInReports(?bool $hideInReports): self
    {
        $this->hideInReports = $hideInReports;

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getAccessRoles(): Collection
    {
        return $this->accessRoles;
    }

    public function addAccessRole(Role $accessRole): self
    {
        if (!$this->accessRoles->contains($accessRole)) {
            $this->accessRoles->add($accessRole);
        }

        return $this;
    }

    public function removeAccessRole(Role $accessRole): self
    {
        $this->accessRoles->removeElement($accessRole);

        return $this;
    }

    #[Groups(['user:read'])]
    public function getPermissions(): array
    {
        $permissions = [];
        /** @var Role $role */
        foreach ($this->accessRoles as $role) {
            /** @var Permission $permission */
            foreach ($role->getPermissions() as $permission) {
                $permissions[] = $permission->getId();
            }
        }

        return array_unique($permissions);
    }

    public function getTelegram(): ?string
    {
        return $this->telegram;
    }

    public function setTelegram(?string $telegram): static
    {
        $this->telegram = $telegram;

        return $this;
    }

    /**
     * @return Collection<int, City>
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function setCities(array $cities): static
    {
        $this->cities = new ArrayCollection($cities);

        return $this;
    }

    public function addCity(City $city): static
    {
        if (!$this->cities->contains($city)) {
            $this->cities->add($city);
        }

        return $this;
    }

    public function removeCity(City $city): static
    {
        $this->cities->removeElement($city);

        return $this;
    }

    public function getCarNumber(): ?string
    {
        return $this->carNumber;
    }

    public function setCarNumber(string $carNumber): static
    {
        $this->carNumber = $carNumber;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function setPatronymic(?string $patronymic): static
    {
        $this->patronymic = $patronymic;

        return $this;
    }
}
