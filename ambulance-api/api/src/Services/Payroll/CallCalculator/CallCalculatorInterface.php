<?php

declare(strict_types=1);

namespace App\Services\Payroll\CallCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\PayrollCalculator;

interface CallCalculatorInterface
{
    public function calculate(Calling $call, PayrollCalculator $payrollCalculator): void;
}
