<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\MedTeam\MedTeam;
use App\Entity\Payroll\ShiftPayroll;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\Payroll\ShiftPayrollRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class UserPayrollShiftsReport extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository $shifts,
        private readonly ShiftPayrollRepository $shiftPayrolls,
    ) {}

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/users/{id}/payroll-report/shifts', name: 'api_user_payroll_report_shifts', methods: 'GET', priority: 10)]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        ini_set('memory_limit', '-1');

        $startDate = $request->query->get('startDate', '2024-12-01');
        $endDate = $request->query->get('endDate', '2025-12-31');

        $startDate = new DateTimeImmutable($startDate);
        $startDate = $startDate->modify('midnight');
        $endDate = new DateTimeImmutable($endDate);
        $endDate = $endDate->modify('+1 day midnight');

        $shifts = $this->shifts->findByPlannedEmployee(
            $startDate,
            $endDate,
            $id
        );

        $items = [];
        $total = 0;
        $fuel = 0;
        $parking = 0;
        $rentCar = 0;
        $time = 0;

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
                'name' => $shift->getPlannedStartAt()->format('d.m.Y'),
                'amount' => '',
                'reward' => 0,
                'subRows' => [],
            ];
        }

        $shiftPayrolls = $this->shiftPayrolls->findByPlannedEmployee(
            $startDate,
            $endDate,
            $id
        );

        /** @var ShiftPayroll $shiftPayroll */
        foreach ($shiftPayrolls as $shiftPayroll) {
            $reward = (float)($shiftPayroll->getAccrued()->amount / 100);

            $items[$shiftPayroll->getShift()->getId()]['reward'] += $reward;
            $total += $reward;

            if ($shiftPayroll->getCalculator()->getProcessor() === 'shift_fuel') {
                $fuel += $reward;
            }elseif ($shiftPayroll->getCalculator()->getProcessor() === 'shift_parking') {
                $parking += $reward;
            }elseif ($shiftPayroll->getCalculator()->getProcessor() === 'shift_rent_car') {
                $rentCar += $reward;
            }else {
                $time += $reward;
            }

            $items[$shiftPayroll->getShift()->getId()]['subRows'][] = [
                'name' => $shiftPayroll->getCalculator()->getName(),
                'amount' => $shiftPayroll->getAmount(),
                'reward' => $reward,
            ];
        }

        return $this->json([
            'items' => array_values($items),
            'total' => $total,
            'fuel' => $fuel,
            'parking' => $parking,
            'rentCar' => $rentCar,
            'time' => $time,
        ]);
    }
}
