<?php

declare(strict_types=1);

namespace App\Services\Payroll\KpiCalculator;

use App\Entity\Money\Money;
use App\Entity\Payroll\KpiDocument\KpiPayroll;
use App\Entity\Payroll\KpiDocument\KpiRecord;
use App\Entity\Payroll\PayrollCalculator;
use App\Repository\CallingRepository;
use App\Repository\Payroll\CallPayrollRepository;
use App\Repository\Payroll\ServicePayrollRepository;
use Exception;

abstract readonly class AbstractKpiProcessor implements KpiProcessorInterface
{
    public function __construct(
        protected CallingRepository $calls,
        private CallPayrollRepository $callPayrolls,
        private ServicePayrollRepository $servicePayrolls,
    ) {}

    final public function calculate(KpiRecord $kpiRecord, PayrollCalculator $calculator): void
    {
        $kpiResult = $this->getKPI($kpiRecord);

        $rates = $this->getRates($calculator);

        $rate = $this->getRate($rates, $kpiResult->kpi);

        $initialAmount = $this->getTheInitialAmountByKPI($kpiRecord);

        $initialAmountByKPI = (int)($initialAmount / 100 * $calculator->getWeight());

        $accrued = (int)($initialAmountByKPI * $rate);

        $payroll = new KpiPayroll(
            $kpiRecord,
            $calculator,
            $kpiRecord->getDocument()->getPeriodEnd(),
            $kpiResult->base,
            $kpiResult->metric,
            $kpiResult->kpi,
            new Money($initialAmountByKPI),
            $rate,
            new Money($accrued)
        );

        $kpiRecord->addKpiPayroll($payroll);
    }

    protected function getKPI(KpiRecord $kpiRecord): KpiResult
    {
        throw new Exception('Not implemented getKPI in ' . static::class);
    }

    protected function getRate(array $rates, float $averageBill): float
    {
        $rate = array_filter($rates, static fn ($range) => $averageBill >= $range['min'] && $averageBill <= $range['max']);

        return (float)(reset($rate)['rate'] ?? 0);
    }

    protected function getTheInitialAmountByKPI(KpiRecord $kpiRecord): int
    {
        $servicesPayrollSum = $this->servicePayrolls->findAccruedSumByAccruedAt(
            $kpiRecord->getDocument()->getPeriodStart(),
            $kpiRecord->getDocument()->getPeriodEnd(),
            $kpiRecord->getEmployee()->getId()
        );

        $callsPayrollSum = $this->callPayrolls->findAccruedSumByAccruedAt(
            $kpiRecord->getDocument()->getPeriodStart(),
            $kpiRecord->getDocument()->getPeriodEnd(),
            $kpiRecord->getEmployee()->getId()
        );

        return $servicesPayrollSum + $callsPayrollSum;
    }

    protected function getRates(PayrollCalculator $calculator): array
    {
        $value = json_decode($calculator->getValue(), true);

        return array_map(static function ($item) {
            return [
                'min' => (float)$item['min'],
                'max' => (float)$item['max'],
                'rate' => (float)$item['rate'],
            ];
        }, $value);
    }
}
