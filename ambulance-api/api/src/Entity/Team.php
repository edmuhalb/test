<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Team\AddLocationAction;
use App\Dto\Team\AddLocation;
use App\Repository\TeamLocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TeamLocationRepository::class)]
#[ORM\Table(name: 'team_teams')]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/locations',
        ),
        new Post(
            uriTemplate: '/locations',
            controller: AddLocationAction::class,
            input: AddLocation::class,
        ),
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['team:read']],
    denormalizationContext: ['groups' => ['team:write']],
    openapi: false,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact'])]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['team:read'])]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Location::class, cascade: ['persist'])]
    private Collection $locations;

    #[ORM\Column(length: 16)]
    #[Groups(['team:read'])]
    private ?string $lon = null;

    #[ORM\Column(length: 16)]
    #[Groups(['team:read'])]
    private ?string $lat = null;

    public function __construct(int $id)
    {
        $this->locations = new ArrayCollection();
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(string $lat, $lon): self
    {
        $this->lat = $lat;
        $this->lon = $lon;
        $this->locations->add(new Location($this, $lat, $lon));

        return $this;
    }

    public function getLon(): ?string
    {
        return $this->lon;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }
}
