<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\Calling\Calling;
use App\Repository\CallingRepository;
use App\Repository\Payroll\PayrollCalculatorRepository;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/my/kpi-hospital', name: 'api_my_kpi_hospital', methods: ['GET'])]
class KpiHospitalAction extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls,
        private readonly PayrollCalculatorRepository $payrollCalculators,
    ) {}

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $userId = $this->getUser()?->getId();
        if (!$userId) {
            throw new DomainException('User not found');
        }

        $payrollCalculator = $this->payrollCalculators->findOneByProcessor('kpi_hospitalization_rate');

        if (!$payrollCalculator) {
            throw new DomainException('Payroll calculator not found');
        }

        if ($request->query->get('period') === 'prev') {
            $startOfMonth = new DateTimeImmutable('first day of last month 00:00:00');
            $endOfMonth = new DateTimeImmutable('first day of this month 00:00:00');
        }else{
            $startOfMonth = new DateTimeImmutable('first day of this month 00:00:00');
            $endOfMonth = new DateTimeImmutable('first day of next month 00:00:00');
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

        return $this->json([
            'value' =>  $kpiValue,
            'rates' => json_decode($payrollCalculator->getValue(), true),
            'rate' => number_format(
                $payrollCalculator->getRate($kpiValue),
                1, '.', ''
            ),
        ]);
    }
}
