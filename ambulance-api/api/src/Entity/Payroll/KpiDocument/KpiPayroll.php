<?php

declare(strict_types=1);

namespace App\Entity\Payroll\KpiDocument;

use App\Entity\Money\Money;
use App\Entity\Payroll\PayrollCalculator;
use App\Repository\Payroll\KpiDocument\KpiPayrollRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: KpiPayrollRepository::class)]
#[ORM\Table(name: 'payroll_employee_kpis')]
class KpiPayroll
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['kpi_document:read'])]
    private ?int $id = null;

    #[ORM\Embedded]
    #[Assert\NotNull]
    #[Groups(['kpi_document:read'])]
    private Money $original;

    #[ORM\Column(nullable: false)]
    #[Groups(['kpi_document:read'])]
    private float $kpi;

    #[ORM\Embedded]
    #[Assert\NotNull]
    #[Groups(['kpi_document:read'])]
    private Money $accrued;

    #[ORM\Column(nullable: false)]
    private DateTimeImmutable $accruedAt;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private PayrollCalculator $calculator;

    #[ORM\ManyToOne(inversedBy: 'kpiPayrolls')]
    #[ORM\JoinColumn(nullable: false)]
    private KpiRecord $record;

    #[ORM\Column(nullable: true)]
    #[Groups(['kpi_document:read'])]
    private ?float $base;

    #[ORM\Column(nullable: true)]
    #[Groups(['kpi_document:read'])]
    private ?float $metric;

    #[ORM\Column(nullable: true)]
    #[Groups(['kpi_document:read'])]
    private ?float $baseKpi;

    public function __construct(
        KpiRecord $record,
        PayrollCalculator $calculator,
        DateTimeImmutable $accruedAt,
        float $base,
        float $metric,
        float $baseKpi,
        Money $original,
        float $kpi,
        Money $accrued,
    ) {
        $this->record = $record;
        $this->calculator = $calculator;
        $this->accruedAt = $accruedAt;
        $this->original = $original;
        $this->kpi = $kpi;
        $this->accrued = $accrued;
        $this->base = $base;
        $this->metric = $metric;
        $this->baseKpi = $baseKpi;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['kpi_document:read'])]
    public function getName(): ?string
    {
        return $this->calculator->getName();
    }

    public function getAccrued(): float
    {
        return $this->accrued->amount / 100;
    }

    public function getAccruedAt(): DateTimeImmutable
    {
        return $this->accruedAt;
    }

    public function getCalculator(): PayrollCalculator
    {
        return $this->calculator;
    }

    public function getKpi(): ?float
    {
        return $this->kpi;
    }

    public function getRecord(): KpiRecord
    {
        return $this->record;
    }

    public function getOriginal(): float
    {
        return $this->original->amount / 100;
    }

    public function getBase(): ?float
    {
        return $this->base;
    }
    public function getMetric(): ?float
    {
        return $this->metric;
    }

    public function getBaseKpi(): ?float
    {
        return $this->baseKpi;
    }
}
