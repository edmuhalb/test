<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Calling\Calling;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ApiResource(
    routePrefix: '/api',
    normalizationContext: ['groups' => ['client:read']],
    denormalizationContext: ['groups' => ['client:write']],
    openapi: false,
)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client:read', 'calling:read', 'client:item:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 11, nullable: true)]
    #[Groups(['client:read', 'client:write', 'calling:read', 'client:item:read'])]
    #[Assert\Regex(
        pattern: '/\d{11}/',
        message: 'Номер телефона должен состоять из 11 цифр.'
    )]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['client:read', 'client:write', 'calling:read', 'client:item:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Calling::class)]
    private Collection $callings;

    public function __construct(
        string $phone,
        ?string $name,
    ) {
        $this->callings = new ArrayCollection();

        $this->phone = preg_replace('/[^0-9]/', '', $phone);
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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
            $calling->setClient($this);
        }

        return $this;
    }

    public function removeCalling(Calling $calling): self
    {
        if ($this->callings->removeElement($calling)) {
            // set the owning side to null (unless already changed)
            if ($calling->getClient() === $this) {
                $calling->setClient(null);
            }
        }

        return $this;
    }
}
