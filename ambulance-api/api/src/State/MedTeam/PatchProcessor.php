<?php

declare(strict_types=1);

namespace App\State\MedTeam;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\MedTeam\MedTeam;
use App\Services\MedTeam\EmployeeNotification;
use App\Services\Payroll\ShiftPayrollCalculator;
use App\Services\WSClient;
use DateTimeImmutable;

readonly class PatchProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface     $processor,
        private WSClient               $wsClient,
        private EmployeeNotification   $employeeNotification,
        private ShiftPayrollCalculator $calculator,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof MedTeam) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $this->wsClient->sendUpdateTeam($data->getId());

        if ($data->isSendSms()) {
            $this->employeeNotification->send($data);
        }

        if ($data->getStatus() !== 'completed') {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        if ($data->getCompletedAt()) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $data->setCompletedAt(new DateTimeImmutable());

        $this->calculator->calculate($data);

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
