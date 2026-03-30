<?php

declare(strict_types=1);

namespace App\Services\Payroll\CallCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Money\Money;
use App\Entity\Payroll\CallPayroll;
use App\Entity\Payroll\PayrollCalculator;
use App\Entity\User\User;
use App\Repository\Payroll\CallPayrollRepository;

class AbstractCallCalculator implements CallCalculatorInterface
{
    public function __construct(
        private readonly CallPayrollRepository $callPayrolls,
    ) {}

    public function calculate(
        Calling $call,
        PayrollCalculator $payrollCalculator,
    ): void {
        $amount = $this->getAmount($call);

        $reward = $amount * (float)$payrollCalculator->getValue();

        if ($reward === 0) {
            return;
        }

        $accrued = new Money(
            (int)($reward * 100)
        );

        $this->process($call, $payrollCalculator, $accrued);
    }

    protected function process(
        Calling $call,
        PayrollCalculator $payrollCalculator,
        Money $accrued,
    ): void {}

    protected function createPayrollForEmployee(
        Calling $call,
        Money $accrued,
        User $employee,
        PayrollCalculator $payrollCalculator
    ): void {
        $payroll = new CallPayroll();
        $payroll->setAccruedAt($call->getCompletedAt());
        $payroll->setCall($call);
        $payroll->setEmployee($employee);
        $payroll->setAccrued($accrued);
        $payroll->setCalculator($payrollCalculator);
        $payroll->setAmount($this->getAmount($call));

        $this->callPayrolls->add($payroll);
    }

    protected function getAmount(Calling $call): float
    {
        $amount = 0;

        foreach ($call->getServices() as $callService) {
            if (
                $callService->getService()?->getEmployeePayrollCalculator()?->getProcessor()
                === 'service_therapy_calculator'
            ) {
                $amount += (float)$callService->getPrice();
            }
        }
        return $amount;
    }
}
