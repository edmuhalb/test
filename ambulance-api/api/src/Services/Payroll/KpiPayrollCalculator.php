<?php

declare(strict_types=1);

namespace App\Services\Payroll;

use App\Entity\Calling\Calling;
use App\Entity\CallType;
use App\Entity\Payroll\KpiDocument\KpiDocument;
use App\Entity\Payroll\KpiDocument\KpiRecord;
use App\Entity\User\User;
use App\Repository\CallingRepository;
use App\Repository\Payroll\PayrollCalculatorRepository;
use App\Services\Payroll\KpiCalculator\KpiProcessorStrategy;
use DateTimeImmutable;

readonly class KpiPayrollCalculator
{
    public function __construct(
        private CallingRepository $calls,
        private PayrollCalculatorRepository $payrollCalculators,
        private KpiProcessorStrategy $processorStrategy
    ) {}

    public function calculate(KpiDocument $document): void
    {
        $document->clearRecords();

        $calls = $this->calls->findAllCompletedByCompletionDateIncludedInPeriod(
            $document->getPeriodStart(),
            $document->getPeriodEnd()
        );

        $users = [];

        /** @var Calling $call */
        foreach ($calls as $call) {
            if ($call->getType() !== CallType::NARCOLOGY) {
                continue;
            }

            $user = $call->getAdmin();

            if ($user && !isset($users[$user->getId()])) {
                $users[$user->getId()] = $user;
            }

            $user = $call->getDoctor();

            if ($user && !isset($users[$user->getId()])) {
                $users[$user->getId()] = $user;
            }
        }

        /** @var User $user */
        foreach ($users as $user) {
            $record = new KpiRecord($document, $user);
            $document->addRecord($record);
            $this->calculateKpi($record);
        }
        $document->setUpdatedAt(new DateTimeImmutable());
    }

    private function calculateKpi(KpiRecord $record): void
    {
        $payrollCalculators = $this->payrollCalculators->findByTarget('payroll');

        foreach ($payrollCalculators as $payrollCalculator) {
            $processor = $this->processorStrategy->getProcessor(
                $payrollCalculator->getProcessor()
            );
            $processor->calculate($record, $payrollCalculator);
        }
    }
}
