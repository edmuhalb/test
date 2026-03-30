<?php

declare(strict_types=1);

namespace App\Services\Payroll\ServiceCalculator;

use App\Entity\Calling\Row;

interface CallPayrollCalculatorProcessorInterface extends PayrollCalculatorProcessorInterface
{
    public function calculate(Row $callService, mixed $rate): void;
}
