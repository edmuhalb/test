<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\Payroll\KpiDocument\KpiPayroll;
use App\Repository\Payroll\KpiDocument\KpiPayrollRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class UserPayrollKPIReport extends AbstractController
{
    public function __construct(
        private readonly KpiPayrollRepository $kpiPayrolls,
    )
    {
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/users/{id}/payroll-report/kpis', name: 'api_user_payroll_report_kpi', methods: 'GET', priority: 10)]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        ini_set('memory_limit', '-1');

        $startDate = $request->query->get('startDate', '2024-12-01');
        $endDate = $request->query->get('endDate', '2024-12-31');

        $startDate = new DateTimeImmutable($startDate);
        $startDate = $startDate->modify('+1 day midnight');
        $endDate = new DateTimeImmutable($endDate);
        $endDate = $endDate->modify('+1 day midnight');

        $shiftPayrolls = $this->kpiPayrolls->findByPlannedEmployee(
            $startDate,
            $endDate,
            $id
        );
        $items = [];
        $total = 0;

        /** @var KpiPayroll $shiftPayroll */
        foreach ($shiftPayrolls as $shiftPayroll) {
            $items[$shiftPayroll->getId()] = [
                'id' => $shiftPayroll->getId(),
                'name' => $shiftPayroll->getCalculator()->getName(),
                'base' => $shiftPayroll->getBase(),
                'metric' => $shiftPayroll->getMetric(),
                'baseKpi' => $shiftPayroll->getBaseKpi(),
                'original' => $shiftPayroll->getOriginal(),
                'kpi' => $shiftPayroll->getKpi(),
                'accrued' => $shiftPayroll->getAccrued(),
            ];
            $total += $shiftPayroll->getAccrued();
        }

        return $this->json([
            'items' => array_values($items),
            'total' => $total,
        ]);
    }
}
