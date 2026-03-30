<?php

declare(strict_types=1);

namespace App\Services\Payroll\KpiCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\KpiDocument\KpiRecord;

final readonly class HospitalizationRate extends AbstractKpiProcessor
{
    protected function getKPI(KpiRecord $kpiRecord): KpiResult
    {
        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $kpiRecord->getDocument()->getPeriodStart(),
            $kpiRecord->getDocument()->getPeriodEnd(),
            $kpiRecord->getEmployee()->getId()
        );

        $countCalls = 0;
        $countStationary = 0;

        /** @var Calling $call */
        foreach ($calls as $call) {
            foreach ($call->getServices() as $callService) {
                if (
                    $callService->isStationary() &&
                    ($callService->getClinic()?->getId() === 1 || $callService->getClinic()?->getId() === 2)
                ) {
                    ++$countStationary;
                }
            }
            ++$countCalls;
        }
        return  new KpiResult(
            $countCalls,
            $countStationary,
            (float)($countStationary > 0 ? $countCalls / $countStationary : 100)
        );
    }
}
