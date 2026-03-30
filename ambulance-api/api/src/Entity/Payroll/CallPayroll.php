<?php

declare(strict_types=1);

namespace App\Entity\Payroll;

use App\Entity\Calling\Calling;
use App\Entity\Money\Money;
use App\Entity\User\User;
use App\Repository\Payroll\CallPayrollRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CallPayrollRepository::class)]
#[ORM\Table(name: 'payroll_employee_calls')]
class CallPayroll
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded]
    #[Assert\NotNull]
    private ?Money $accrued = null;

    #[ORM\Column]
    private ?DateTimeImmutable $accruedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employee = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Calling $call = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PayrollCalculator $calculator = null;

    #[ORM\Column(nullable: true)]
    private ?float $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccrued(): ?Money
    {
        return $this->accrued;
    }

    public function setAccrued(Money $value): void
    {
        $this->accrued = $value;
    }

    public function getAccruedAt(): ?DateTimeImmutable
    {
        return $this->accruedAt;
    }

    public function setAccruedAt(DateTimeImmutable $accruedAt): static
    {
        $this->accruedAt = $accruedAt;

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getCall(): ?Calling
    {
        return $this->call;
    }

    public function setCall(?Calling $call): static
    {
        $this->call = $call;

        return $this;
    }

    public function getCalculator(): ?PayrollCalculator
    {
        return $this->calculator;
    }

    public function setCalculator(?PayrollCalculator $calculator): static
    {
        $this->calculator = $calculator;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }
}
