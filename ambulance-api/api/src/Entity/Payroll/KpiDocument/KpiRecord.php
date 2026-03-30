<?php

declare(strict_types=1);

namespace App\Entity\Payroll\KpiDocument;

use App\Entity\User\User;
use App\Repository\Payroll\KpiDocument\KpiRecordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: KpiRecordRepository::class)]
#[ORM\Table(name: 'payroll_kpi_documents_records')]
class KpiRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['kpi_document:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'records')]
    #[ORM\JoinColumn(nullable: false)]
    private KpiDocument $document;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['kpi_document:read'])]
    private User $employee;

    #[ORM\OneToMany(
        mappedBy: 'record',
        targetEntity: KpiPayroll::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[Groups(['kpi_document:read'])]
    #[ORM\OrderBy(['calculator' => 'ASC'])]
    private Collection $kpiPayrolls;

    public function __construct(KpiDocument $document, User $employee)
    {
        $this->document = $document;
        $this->employee = $employee;
        $this->kpiPayrolls = new ArrayCollection();
    }

    #[Groups(['kpi_document:read'])]
    public function getAccrue(): float
    {
        $result = 0;
        foreach ($this->kpiPayrolls as $kpiPayroll) {
            $result += $kpiPayroll->getAccrued();
        }
        return $result;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): KpiDocument
    {
        return $this->document;
    }

    public function getEmployee(): User
    {
        return $this->employee;
    }

    public function getKpiPayrolls(): Collection
    {
        return $this->kpiPayrolls;
    }

    public function addKpiPayroll(KpiPayroll $kpiPayroll): static
    {
        $this->kpiPayrolls->add($kpiPayroll);

        return $this;
    }

    public function removeKpiPayroll(KpiPayroll $kpiPayroll): static
    {
        $this->kpiPayrolls->removeElement($kpiPayroll);

        return $this;
    }
}
