<?php

declare(strict_types=1);

namespace App\Services\Payroll\ShiftCalculator;

use App\Entity\MedTeam\MedTeam;
use App\Entity\Money\Money;
use App\Entity\Payroll\PayrollCalculator;
use App\Entity\Payroll\ShiftPayroll;
use App\Repository\Payroll\ShiftPayrollRepository;

readonly class RentCarCalculator implements ShiftCalculatorInterface
{
    public function __construct(
        private ShiftPayrollRepository $shiftPayrolls,
    ) {}

    public function calculate(MedTeam $shift, PayrollCalculator $payrollCalculator): void
    {

        $accruedAt = $shift->getPlannedFinishAt();

        if (!$accruedAt) {
            return;
        }

        if ($shift->getCar()) {
            return;
        }

        $employee = $shift->getAdmin();

        if (!$employee) {
            return;
        }

        $diff = $shift->getPlannedFinishAt()->diff($shift->getPlannedStartAt());
        $hours = $diff->days * 24 + $diff->h;

        $rate = (float)$payrollCalculator->getValue();

        $accrued = new Money(
            (int)(((float)$hours * $rate) * 100)
        );

        $shiftPayroll = new ShiftPayroll();
        $shiftPayroll
            ->setAccruedAt($accruedAt)
            ->setAccrued($accrued)
            ->setCalculator($payrollCalculator)
            ->setEmployee($employee)
            ->setAmount($hours)
            ->setShift($shift);

        $this->shiftPayrolls->add($shiftPayroll);
    }
}
