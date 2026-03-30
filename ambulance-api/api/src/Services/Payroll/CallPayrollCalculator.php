<?php

declare(strict_types=1);

namespace App\Services\Payroll;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\CallType;
use App\Repository\Payroll\CallPayrollRepository;
use App\Repository\Payroll\PayrollCalculatorRepository;
use App\Repository\Payroll\ServicePayrollRepository;
use App\Services\Payroll\CallCalculator\CallCalculatorStrategy;
use App\Services\Payroll\ServiceCalculator\ServiceCalculatorStrategy;

readonly class CallPayrollCalculator
{
    public function __construct(
        private ServicePayrollRepository $servicePayrolls,
        private PayrollCalculatorRepository $payrollCalculators,
        private CallCalculatorStrategy $callCalculatorStrategy,
        private ServiceCalculatorStrategy $serviceCalculatorStrategy,
        private CallPayrollRepository $callPayrolls,
    ) {}

    public function calculate(Calling $call): void
    {
        if ($call->getStatus() !== Status::COMPLETED) {
            return;
        }

        if ($call->getType() !== CallType::NARCOLOGY) {
            return;
        }

        $this->calculateService($call);
        $this->calculateCall($call);
    }

    private function calculateService(Calling $call): void
    {
        foreach ($call->getServices() as $callService) {
            /** @var string|null $calculator */
            $calculator = $callService->getService()?->getEmployeePayrollCalculator()?->getProcessor();
            if ($calculator === null) {
                continue;
            }

            /** @var mixed $value */
            $value = $callService->getService()?->getEmployeePayrollCalculator()?->getValue();

            // TODO убрать удаление в калькулятор
            $this->servicePayrolls->removeByCallServiceId($callService->getId());

            $processor = $this->serviceCalculatorStrategy->getProcessor($calculator);

            $processor->calculate($callService, $value);
        }
    }

    private function calculateCall(Calling $call): void
    {
        // TODO убрать удаление в калькулятор
        $this->callPayrolls->removeByCallServiceId($call->getId());
        $payrollCalculators = $this->payrollCalculators->findByTarget('call');

        foreach ($payrollCalculators as $payrollCalculator) {
            $processor = $this->callCalculatorStrategy->getStrategy(
                $payrollCalculator->getProcessor()
            );
            $processor->calculate($call, $payrollCalculator);
        }
    }
}
