<?php

declare(strict_types=1);

namespace App\Services\Payroll\ShiftCalculator;

use App\Entity\MedTeam\MedTeam;
use App\Entity\Payroll\PayrollCalculator;

interface ShiftCalculatorInterface
{
    public function calculate(MedTeam $shift, PayrollCalculator $payrollCalculator): void;
}
