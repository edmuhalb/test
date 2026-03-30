<?php

declare(strict_types=1);

namespace App\Services\Payroll\ShiftCalculator;

use App\Entity\MedTeam\MedTeam;
use App\Entity\Money\Money;
use App\Entity\Payroll\PayrollCalculator;
use App\Entity\Payroll\ShiftPayroll;
use App\Repository\Payroll\ShiftPayrollRepository;

readonly class ParkingCalculator implements ShiftCalculatorInterface
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

        $report = $shift->getTransportReport();

        if ($report === null) {
            return;
        }

        $employee = $shift->getAdmin();

        if (!$employee) {
            return;
        }

        if (!$report->getParkingFees() && !$report->getToolRoad()) {
            return;
        }

        $rate = (float)$payrollCalculator->getValue();

        $sum = (float)$report->getParkingFees() + (float)$report->getToolRoad();

        $accrued = new Money(
            (int)(($sum * $rate) * 100)
        );

        $shiftPayroll = new ShiftPayroll();
        $shiftPayroll
            ->setAccruedAt($accruedAt)
            ->setAccrued($accrued)
            ->setCalculator($payrollCalculator)
            ->setEmployee($employee)
            ->setAmount($sum)
            ->setShift($shift);

        $this->shiftPayrolls->add($shiftPayroll);
    }
}
