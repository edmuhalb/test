<?php

declare(strict_types=1);

namespace App\Services\Payroll\KpiCalculator;

use App\Entity\Payroll\KpiDocument\KpiRecord;
use App\Entity\Payroll\PayrollCalculator;

interface KpiProcessorInterface
{
    public function calculate(KpiRecord $kpiRecord, PayrollCalculator $calculator): void;
}
