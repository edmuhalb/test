<?php

declare(strict_types=1);

namespace App\Services\Payroll;

use App\Entity\CallType;
use App\Entity\MedTeam\MedTeam;
use App\Repository\Payroll\PayrollCalculatorRepository;
use App\Repository\Payroll\ShiftPayrollRepository;
use App\Services\Payroll\ShiftCalculator\ShiftCalculatorStrategy;

readonly class ShiftPayrollCalculator
{
    public function __construct(
        private PayrollCalculatorRepository $payrollCalculators,
        private ShiftPayrollRepository $shiftPayrolls,
        private ShiftCalculatorStrategy $strategies,
    ) {}

    public function calculate(MedTeam $shift): void
    {
        if ($shift->getCallType() !== CallType::NARCOLOGY) {
            return;
        }

        // TODO убрать удаление в калькулятор
        $this->shiftPayrolls->removeByShiftId($shift->getId());

        $payrollCalculators = $this->payrollCalculators->findByTarget('shift');

        foreach ($payrollCalculators as $payrollCalculator) {
            $processor = $this->strategies->getStrategy(
                $payrollCalculator->getProcessor()
            );
            $processor->calculate($shift, $payrollCalculator);
        }
    }
}
