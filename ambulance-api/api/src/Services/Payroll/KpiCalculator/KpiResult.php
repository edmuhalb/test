<?php

namespace App\Services\Payroll\KpiCalculator;

class KpiResult
{
    public function __construct(
        public readonly float $base,
        public readonly float $metric,
        public readonly float $kpi,
    )
    {
    }
}