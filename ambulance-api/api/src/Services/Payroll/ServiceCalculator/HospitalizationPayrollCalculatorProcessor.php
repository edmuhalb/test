<?php

declare(strict_types=1);

namespace App\Services\Payroll\ServiceCalculator;

use App\Entity\Calling\Row;
use App\Entity\Money\Money;
use App\Entity\Payroll\ServicePayroll;
use App\Entity\User\User;
use App\Repository\Payroll\ServicePayrollRepository;

readonly class HospitalizationPayrollCalculatorProcessor implements CallPayrollCalculatorProcessorInterface
{
    public function __construct(
        private ServicePayrollRepository $servicePayrolls,
    ) {}

    public function calculate(Row $callService, mixed $rate): void
    {
        $admin = $callService->getCalling()->getAdmin();

        $this->createPayrollForEmployee($callService, $rate, $admin);

        $doctor = $callService->getCalling()->getDoctor();

        $this->createPayrollForEmployee($callService, $rate, $doctor);
    }

    public function createPayrollForEmployee(Row $callService, mixed $rate, ?User $employee): void
    {
        if (!$employee) {
            return;
        }

        $accrued = new Money(
            (int)(($callService->getPrice() * $rate) * 100)
        );

        $payroll = new ServicePayroll();
        $payroll->setAccruedAt($callService->getCalling()->getCompletedAt());
        $payroll->setCallService($callService);
        $payroll->setEmployee($employee);
        $payroll->setAccrued($accrued);
        $payroll->setAmount($callService->getPrice());
        $this->servicePayrolls->add($payroll);
    }
}
