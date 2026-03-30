<?php

declare(strict_types=1);

namespace App\Services\Payroll\KpiCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\KpiDocument\KpiRecord;

readonly class RepeatRate extends AbstractKpiProcessor
{
    protected function getKPI(KpiRecord $kpiRecord): KpiResult
    {
        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $kpiRecord->getDocument()->getPeriodStart(),
            $kpiRecord->getDocument()->getPeriodEnd(),
            $kpiRecord->getEmployee()->getId()
        );

        $countCalls = 0;
        $countRepeat = 0;

        /** @var Calling $call */
        foreach ($calls as $call) {
            foreach ($call->getServices() as $callService) {
                if ($callService->isReplay()) {
                    ++$countRepeat;
                }
            }
            ++$countCalls;
        }

        return new KpiResult(
            $countCalls,
            $countRepeat,
            (float)($countRepeat > 0 ? $countCalls / $countRepeat : 100)
        );
    }
}
