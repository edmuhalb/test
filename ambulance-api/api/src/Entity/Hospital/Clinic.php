<?php

declare(strict_types=1);

namespace App\Entity\Hospital;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Hospital\ClinicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClinicRepository::class)]
#[ApiResource(
    routePrefix: '/api',
    normalizationContext: ['groups' => ['clinic:read', 'clinic:item:read']],
    denormalizationContext: ['groups' => ['clinic:write']],
    openapi: false,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
class Clinic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['clinic:read', 'clinic:write', 'hospital:read', 'v1-call:read', 'v1-call:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['clinic:read', 'clinic:write', 'hospital:read', 'v1-call:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'clinic', targetEntity: Hospital::class)]
    private Collection $hospitals;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['clinic:read', 'clinic:write', 'hospital:read'])]
    private ?string $shortName = null;

    public function __construct()
    {
        $this->hospitals = new ArrayCollection();
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

    /**
     * @return Collection<int, Hospital>
     */
    public function getHospitals(): Collection
    {
        return $this->hospitals;
    }

    public function addHospital(Hospital $hospital): self
    {
        if (!$this->hospitals->contains($hospital)) {
            $this->hospitals->add($hospital);
            $hospital->setClinic($this);
        }

        return $this;
    }

    public function removeHospital(Hospital $hospital): self
    {
        if ($this->hospitals->removeElement($hospital)) {
            // set the owning side to null (unless already changed)
            if ($hospital->getClinic() === $this) {
                $hospital->setClinic(null);
            }
        }

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): static
    {
        $this->shortName = $shortName;

        return $this;
    }
}
