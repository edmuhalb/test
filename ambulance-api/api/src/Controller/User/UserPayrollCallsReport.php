<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\CallPayroll;
use App\Entity\Payroll\ServicePayroll;
use App\Repository\CallingRepository;
use App\Repository\Payroll\CallPayrollRepository;
use App\Repository\Payroll\ServicePayrollRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class UserPayrollCallsReport extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls,
        private readonly CallPayrollRepository $callPayrolls,
        private readonly ServicePayrollRepository $servicePayrolls
    ) {}

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/users/{id}/payroll-report/calls', name: 'api_user_payroll_report_calls', methods: 'GET', priority: 10)]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        ini_set('memory_limit', '-1');

        $startDate = $request->query->get('startDate', '2024-12-01');
        $endDate = $request->query->get('endDate', '2024-12-31');

        $startDate = new DateTimeImmutable($startDate);
        $startDate = $startDate->modify('midnight');
        $endDate = new DateTimeImmutable($endDate);
        $endDate = $endDate->modify('+1 day midnight');


        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $startDate,
            $endDate,
            $id
        );

        $items = [];
        $total = 0;

        $callsItems = [];

        /** @var Calling $call */
        foreach ($calls as $call) {
            $callsItems[$call->getId()] = [
                'id' => $call->getId(),
                'completedAt' => $call->getCompletedAt()->format('d.m.Y H:i'),
                'completedDate' => $call->getCompletedAt()->format('d.m.Y'),
                'admin' => $call->getAdmin() ? [
                    'id' => $call->getAdmin()->getId(),
                    'name' => $call->getAdmin()->getName(),
                ] : null,
                'doctor' => $call->getDoctor() ? [
                    'id' => $call->getDoctor()->getId(),
                    'name' => $call->getDoctor()->getName(),
                ] : null,
                'callId' => $call->getId(),
                'name' => $call->getAddress(),
                'amount' => '',
                'reward' => 0,
                'subRows' => [],
            ];
        }

        $servicePayrolls = $this->servicePayrolls->findByPlannedEmployee(
            $startDate,
            $endDate,
            $id
        );

        /** @var ServicePayroll $servicePayroll */
        foreach ($servicePayrolls as $servicePayroll) {
            $reward = (float)($servicePayroll->getAccrued()->amount / 100);

            $callsItems[$servicePayroll->getCallService()->getCalling()->getId()]['reward'] += $reward;

            $callsItems[$servicePayroll->getCallService()->getCalling()->getId()]['subRows'][] = [
                'name' => $servicePayroll->getCallService()->getService()->getName(),
                'amount' => $servicePayroll->getCallService()->getPrice(),
                'reward' => $reward,
            ];
        }

        $callPayrolls = $this->callPayrolls->findByPlannedEmployee(
            $startDate,
            $endDate,
            $id
        );

        /** @var CallPayroll $callPayroll */
        foreach ($callPayrolls as $callPayroll) {
            if (!isset($callsItems[$callPayroll->getCall()->getId()])) {
                $call = $this->calls->getById($callPayroll->getCall()->getId());

                $callsItems[$call->getId()] = [
                    'id' => $call->getId(),
                    'completedAt' => $call->getCompletedAt()->format('d.m.Y H:i'),
                    'completedDate' => $call->getCompletedAt()->format('d.m.Y'),
                    'admin' => $call->getAdmin() ? [
                        'id' => $call->getAdmin()->getId(),
                        'name' => $call->getAdmin()->getName(),
                    ] : null,
                    'doctor' => $call->getDoctor() ? [
                        'id' => $call->getDoctor()->getId(),
                        'name' => $call->getDoctor()->getName(),
                    ] : null,
                    'callId' => $call->getId(),
                    'name' => $call->getAddress(),
                    'amount' => '',
                    'reward' => 0,
                    'subRows' => [],
                ];
            }
            $reward = (float)($callPayroll->getAccrued()->amount / 100);

            $callsItems[$callPayroll->getCall()->getId()]['reward'] += $reward;

            $callsItems[$callPayroll->getCall()->getId()]['subRows'][] = [
                'name' => $callPayroll->getCalculator()->getName(),
                'amount' => '',
                'reward' => $reward,
            ];
        }

        foreach ($callsItems as $callItem) {
            if (!isset($items[$callItem['completedDate']])) {
                $items[$callItem['completedDate']] = [
                    'name' => $callItem['completedDate'],
                    'amount' => '',
                    'reward' => 0,
                    'subRows' => [],
                ];
            }

            $items[$callItem['completedDate']]['reward'] += $callItem['reward'];

            $items[$callItem['completedDate']]['subRows'][] = $callItem;
            $total += $callItem['reward'];
        }

        return $this->json([
            'items' => array_values($items),
            'total' => $total,
        ]);
    }
}
