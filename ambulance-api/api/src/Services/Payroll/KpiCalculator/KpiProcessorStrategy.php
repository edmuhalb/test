<?php

declare(strict_types=1);

namespace App\Services\Payroll\KpiCalculator;

use DomainException;

class KpiProcessorStrategy
{
    private array $processors = [];

    public function __construct(
        AverageBill $averageBill,
        RepeatRate $repeatRate,
        HospitalizationRate $hospitalizationRate
    ) {
        $this->processors = [
            'kpi_average_bill' => $averageBill,
            'kpi_repeat_rate' => $repeatRate,
            'kpi_hospitalization_rate' => $hospitalizationRate,
        ];
    }

    public function getProcessor($processor): KpiProcessorInterface
    {
        if (!isset($this->processors[$processor])) {
            throw new DomainException(
                'Unknown payroll kpi processor ' .
                $processor
            );
        }

        return $this->processors[$processor];
    }
}
