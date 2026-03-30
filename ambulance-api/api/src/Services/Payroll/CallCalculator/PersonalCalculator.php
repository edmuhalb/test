<?php

declare(strict_types=1);

namespace App\Services\Payroll\CallCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Money\Money;
use App\Entity\Payroll\PayrollCalculator;

class PersonalCalculator extends AbstractCallCalculator
{
    protected function process(
        Calling $call,
        PayrollCalculator $payrollCalculator,
        Money $accrued,
    ): void {
        if ($call->getCountRepeat() > 0) {
            return;
        }

        if (!$call->isPersonal()){
            return;
        }

        $admin = $call->getAdmin();

        $this->createPayrollForEmployee($call, $accrued, $admin, $payrollCalculator);

        $doctor = $call->getDoctor();

        $this->createPayrollForEmployee($call, $accrued, $doctor, $payrollCalculator);
    }

    protected function getAmount(Calling $call): float
    {
        $amount = 0;

        foreach ($call->getServices() as $callService) {
            if (
                $callService->getService()?->getEmployeePayrollCalculator()?->getProcessor()
                === 'service_therapy_calculator' ||
                $callService->getService()?->getEmployeePayrollCalculator()?->getProcessor()
                === 'service_hospitalization_calculator' ||
                $callService->getService()?->getEmployeePayrollCalculator()?->getProcessor()
                === 'service_transportation_calculator'
            ) {
                $amount += (float)$callService->getPrice();
            }
        }
        foreach ($call->getServices() as $callService) {
            if (
                $callService->getService()?->getEmployeePayrollCalculator()?->getProcessor()
                === 'service_codding_calculator'
            ) {
                $amount += (float)$callService->getPrice() - $callService->getService()->getCoastPrice();
            }
        }
        return $amount;
    }
}
