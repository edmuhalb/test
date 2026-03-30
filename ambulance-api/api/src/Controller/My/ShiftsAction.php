<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\MedTeam\MedTeam;
use App\Entity\Payroll\ShiftPayroll;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\Payroll\ShiftPayrollRepository;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/my/shifts', name: 'api_my_shifts', methods: ['GET'])]
class ShiftsAction extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository      $shifts,
        private readonly ShiftPayrollRepository $shiftPayrolls,
    )
    {
    }

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        ini_set('memory_limit', '-1');

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

        $shifts = $this->shifts->findByPlannedEmployee(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        $items = [];
        $count = 0;
        $total = 0;

        $shiftIds = [];

        /** @var MedTeam $shift */
        foreach ($shifts as $shift) {
            $items[$shift->getId()] = [
                'id' => $shift->getId(),
                'startedAt' => $shift->getPlannedStartAt()->format('d.m.Y H:i'),
                'finishedAt' => $shift->getPlannedFinishAt()->format('d.m.Y H:i'),
                'car' => $shift->getCar() ? [
                    'id' => $shift->getCar()->getId(),
                    'name' => $shift->getCar()->getName(),
                ] : null,
                'admin' => $shift->getAdmin() ? [
                    'id' => $shift->getAdmin()->getId(),
                    'name' => $shift->getAdmin()->getName(),
                ] : null,
                'doctor' => $shift->getDoctor() ? [
                    'id' => $shift->getDoctor()->getId(),
                    'name' => $shift->getDoctor()->getName(),
                ] : null,
                'name' => $shift->getPlannedStartAt()->format('d.m.Y H:i') . ' - ' . $shift->getPlannedFinishAt()->format('d.m.Y H:i'),
                'amount' => '',
                'reward' => 0,
                'subRows' => [],
            ];

            $count++;
            $shiftIds[] = $shift->getId();
        }


        $shiftPayrolls = $this->shiftPayrolls->findByShiftIds(
            $shiftIds,
            $userId
        );

        /** @var ShiftPayroll $shiftPayroll */
        foreach ($shiftPayrolls as $shiftPayroll) {
            if ($shiftPayroll->getAccruedAt() < $startOfMonth || $shiftPayroll->getAccruedAt() >= $endOfMonth) {
                continue;
            }
            if ($shiftPayroll->getAmount() == 0) {
                continue;
            }
            $reward = (float)($shiftPayroll->getAccrued()->amount / 100);

            $items[$shiftPayroll->getShift()->getId()]['reward'] += $reward;
            $total += $reward;

            $addedName = '';

            if (
                $shiftPayroll->getCalculator()->getProcessor() !== 'shift_fuel' &&
                $shiftPayroll->getCalculator()->getProcessor() !== 'shift_parking' &&
                $shiftPayroll->getCalculator()->getProcessor() !== 'shift_rent_car'
            ) {
                $addedName = ' ' . $shiftPayroll->getAccruedAt()->format('d.m.Y');
            }

            $items[$shiftPayroll->getShift()->getId()]['subRows'][] = [
                'name' => $shiftPayroll->getCalculator()->getName() . $addedName,
                'amount' => $shiftPayroll->getAmount(),
                'reward' => $reward,
            ];
        }

        return $this->json([
            'items' => array_values($items),
            'total' => $total,
            'count' => $count,
        ]);
    }
}
