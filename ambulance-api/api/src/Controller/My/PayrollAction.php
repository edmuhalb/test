<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\Calling\Calling;
use App\Entity\MedTeam\MedTeam;
use App\Entity\Payroll\CallPayroll;
use App\Entity\Payroll\ServicePayroll;
use App\Entity\Payroll\ShiftPayroll;
use App\Repository\CallingRepository;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\Payroll\CallPayrollRepository;
use App\Repository\Payroll\PayrollCalculatorRepository;
use App\Repository\Payroll\ServicePayrollRepository;
use App\Repository\Payroll\ShiftPayrollRepository;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/my/payroll', name: 'api_my_payroll', methods: ['GET'])]
class PayrollAction extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository           $shifts,
        private readonly ShiftPayrollRepository      $shiftPayrolls,
        private readonly CallingRepository           $calls,
        private readonly CallPayrollRepository       $callPayrolls,
        private readonly ServicePayrollRepository    $servicePayrolls,
        private readonly PayrollCalculatorRepository $payrollCalculators,
    )
    {
    }

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $userId = $this->getUser()?->getId();
        if (!$userId) {
            throw new DomainException('User not found');
        }

        if ($request->query->get('period') === 'prev') {
            $startOfMonth = new DateTimeImmutable('first day of last month 00:00:00');
            $endOfMonth = new DateTimeImmutable('first day of this month 00:00:00');
        }else{
            $startOfMonth = new DateTimeImmutable('first day of this month 00:00:00');
            $endOfMonth = new DateTimeImmutable('first day of next month 00:00:00');
        }

        [
            $hoursPayroll,
            $fuelPayroll,
            $parkingPayroll,
            $rentCarPayroll
        ] = $this->calcShifts($startOfMonth, $endOfMonth, $userId);

        $callsPayroll = $this->calcCalls($startOfMonth, $endOfMonth, $userId);

        $payrollPayroll = $hoursPayroll + $callsPayroll;

        $kpiPayroll = $this->calcKpiPayroll($startOfMonth, $endOfMonth, $userId, $payrollPayroll);

        $totalPayroll = $payrollPayroll + $kpiPayroll + $fuelPayroll + $parkingPayroll + $rentCarPayroll;

        return $this->json([
            'hoursPayroll' => $hoursPayroll,
            'fuelPayroll' => $fuelPayroll,
            'parkingPayroll' => $parkingPayroll,
            'rentCarPayroll' => $rentCarPayroll,
            'callsPayroll' => $callsPayroll,
            'payrollPayroll' => $payrollPayroll,
            'kpiPayroll' => $kpiPayroll,
            'totalPayroll' => $totalPayroll,
        ]);
    }

    private function calcKpiPayroll(
        DateTimeImmutable $startOfMonth,
        DateTimeImmutable $endOfMonth,
        int               $userId,
                          $payrollPayroll
    ): float|int
    {
        $kpiPayroll = 0;

        $kpiPayroll += $this->getKpiAverageBillPayroll($startOfMonth, $endOfMonth, $userId, $payrollPayroll);
        $kpiPayroll += $this->getKpiRepeatPayroll($startOfMonth, $endOfMonth, $userId, $payrollPayroll);
        $kpiPayroll += $this->getKpiHospitalPayroll($startOfMonth, $endOfMonth, $userId, $payrollPayroll);

        return $kpiPayroll;
    }

    private function getKpiHospitalPayroll(
        DateTimeImmutable $startOfMonth,
        DateTimeImmutable $endOfMonth,
        int               $userId,
                          $payrollPayroll
    ): int
    {
        $payrollCalculator = $this->payrollCalculators->findOneByProcessor('kpi_hospitalization_rate');

        if (!$payrollCalculator) {
            throw new DomainException('Payroll calculator not found');
        }

        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $startOfMonth,
            $endOfMonth,
            $userId
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

        $kpiValue = (float)($countStationary > 0 ? $countCalls / $countStationary : 100);

        $rate = $payrollCalculator->getRate($kpiValue);

        $initialAmountByKPI = (int)($payrollPayroll / 100 * $payrollCalculator->getWeight());

        return (int)($initialAmountByKPI * $rate);
    }

    private function getKpiAverageBillPayroll(
        DateTimeImmutable $startOfMonth,
        DateTimeImmutable $endOfMonth,
        int               $userId,
                          $payrollPayroll
    ): int
    {
        $payrollCalculator = $this->payrollCalculators->findOneByProcessor('kpi_average_bill');

        if (!$payrollCalculator) {
            throw new DomainException('Payroll calculator not found');
        }

        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        $count = 0;
        $price = 0;

        /** @var Calling $call */
        foreach ($calls as $call) {
            $price += $call->getPrice() ?: 0;
            ++$count;
        }

        $averageBill = (float)($count > 0 ? $price / $count : 0);

        $rate = $payrollCalculator->getRate($averageBill);

        $initialAmountByKPI = (int)($payrollPayroll / 100 * $payrollCalculator->getWeight());

        return (int)($initialAmountByKPI * $rate);
    }

    private function getKpiRepeatPayroll(
        DateTimeImmutable $startOfMonth,
        DateTimeImmutable $endOfMonth,
        int               $userId,
                          $payrollPayroll
    ): int
    {
        $payrollCalculator = $this->payrollCalculators->findOneByProcessor('kpi_repeat_rate');

        if (!$payrollCalculator) {
            throw new DomainException('Payroll calculator not found');
        }

        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $startOfMonth,
            $endOfMonth,
            $userId
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

        $kpiValue = (float)($countRepeat > 0 ? $countCalls / $countRepeat : 100);
        $rate = $payrollCalculator->getRate($kpiValue);

        $initialAmountByKPI = (int)($payrollPayroll / 100 * $payrollCalculator->getWeight());

        return (int)($initialAmountByKPI * $rate);
    }

    private function calcCalls(DateTimeImmutable $startOfMonth, DateTimeImmutable $endOfMonth, int $userId): float|int
    {
        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        $total = 0;
        $callIds = [];
        $rowIds = [];

        /** @var Calling $call */
        foreach ($calls as $call) {
            $callIds[] = $call->getId();
            foreach ($call->getServices() as $row) {
                $rowIds[] = $row->getId();
            }
        }

        $servicePayrolls = $this->servicePayrolls->findByRowIds($rowIds, $userId);

        /** @var ServicePayroll $servicePayroll */
        foreach ($servicePayrolls as $servicePayroll) {
            $total += (float)($servicePayroll->getAccrued()->amount / 100);
        }

        //$callPayrolls = $this->callPayrolls->findByCallIds($callIds, $userId);
        $callPayrolls = $this->callPayrolls->findByPlannedEmployee(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        /** @var CallPayroll $callPayroll */
        foreach ($callPayrolls as $callPayroll) {
            $total += (float)($callPayroll->getAccrued()->amount / 100);
        }

        return $total;
    }

    private function calcShifts(DateTimeImmutable $startOfMonth, DateTimeImmutable $endOfMonth, int $userId): array
    {
        $hoursPayroll = 0;
        $fuelPayroll = 0;
        $parkingPayroll = 0;
        $rentCarPayroll = 0;

        $shifts = $this->shifts->findByPlannedEmployee(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        $shiftIds = [];

        /** @var MedTeam $shift */
        foreach ($shifts as $shift) {
            $shiftIds[] = $shift->getId();
        }


        $shiftPayrolls = $this->shiftPayrolls->findByShiftIds(
            $shiftIds,
            $userId
        );

        /** @var ShiftPayroll $shiftPayroll */
        foreach ($shiftPayrolls as $shiftPayroll) {
            if ($shiftPayroll->getAccruedAt() < $startOfMonth || $shiftPayroll->getAccruedAt() > $endOfMonth) {
                continue;
            }
            if ($shiftPayroll->getAmount() == 0) {
                continue;
            }
            $reward = (float)($shiftPayroll->getAccrued()->amount / 100);

            if ($shiftPayroll->getCalculator()->getProcessor() === 'shift_fuel') {
                $fuelPayroll += $reward;
            } elseif ($shiftPayroll->getCalculator()->getProcessor() === 'shift_parking') {
                $parkingPayroll += $reward;
            } elseif ($shiftPayroll->getCalculator()->getProcessor() === 'shift_rent_car') {
                $rentCarPayroll += $reward;
            } else {
                $hoursPayroll += $reward;
            }
        }

        return [
            $hoursPayroll,
            $fuelPayroll,
            $parkingPayroll,
            $rentCarPayroll
        ];
    }
}
