<?php

declare(strict_types=1);

namespace App\Entity\Service;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Calling\Row;
use App\Entity\Payroll\PayrollCalculator;
use App\Repository\Service\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\Table(name: 'service_services')]
#[ApiResource(
    routePrefix: '/api',
    normalizationContext: ['groups' => ['service:read', 'service:item:read', 'service-category:item:read']],
    denormalizationContext: ['groups' => ['service:write']],
    openapi: false,
    order: ['sort' => 'ASC'],
)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service:item:read', 'exchange_calling:read', 'v1-call:read', 'v1-call:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['service:item:read', 'service:write', 'exchange_calling:read', 'v1-call:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Row::class)]
    private Collection $rows;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(choices: [
        'replay',
        'hospital',
        'default',
    ])]
    #[Groups(['service:item:read', 'service:write', 'exchange_calling:read'])]
    private ?string $type = 'default';

    #[ORM\Column(nullable: true)]
    #[Groups(['service:item:read', 'service:write'])]
    private ?int $sort = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[Groups(['service:item:read', 'service:write', 'exchange_calling:read'])]
    private ?Category $category = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['service:item:read', 'service:write', 'exchange_calling:read'])]
    private ?int $coastPrice = 0;

    #[ORM\ManyToOne]
    #[Groups(['service:item:read', 'service:write'])]
    #[Assert\NotNull]
    private ?PayrollCalculator $employeePayrollCalculator = null;

    public function __construct()
    {
        $this->rows = new ArrayCollection();
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
     * @return Collection<int, Row>
     */
    public function getRows(): Collection
    {
        return $this->rows;
    }

    public function addRow(Row $row): self
    {
        if (!$this->rows->contains($row)) {
            $this->rows->add($row);
            $row->setService($this);
        }

        return $this;
    }

    public function removeRow(Row $row): self
    {
        if ($this->rows->removeElement($row)) {
            // set the owning side to null (unless already changed)
            if ($row->getService() === $this) {
                $row->setService(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCoastPrice(): ?int
    {
        return $this->coastPrice;
    }

    public function setCoastPrice(?int $coastPrice): self
    {
        $this->coastPrice = $coastPrice;

        return $this;
    }

    public function isRepeatDesign(): bool
    {
        return $this->type === 'replay';
    }

    public function getEmployeePayrollCalculator(): ?PayrollCalculator
    {
        return $this->employeePayrollCalculator;
    }

    public function setEmployeePayrollCalculator(?PayrollCalculator $employeePayrollCalculator): static
    {
        $this->employeePayrollCalculator = $employeePayrollCalculator;

        return $this;
    }
}
