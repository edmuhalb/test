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

#[Route('/api/my/average-bill', name: 'api_my_average_bill', methods: ['GET'])]
class AverageBillAction extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls,
        private readonly PayrollCalculatorRepository $payrollCalculators,
    ) {}

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        /**
         * 'kpi_average_bill' => $averageBill,
         * 'kpi_repeat_rate' => $repeatRate,
         * 'kpi_hospitalization_rate' => $hospitalizationRate,
         */

        $userId = $this->getUser()?->getId();
        if (!$userId) {
            throw new DomainException('User not found');
        }

        $payrollCalculator = $this->payrollCalculators->findOneByProcessor('kpi_average_bill');

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

        $count = 0;
        $price = 0;

        /** @var Calling $call */
        foreach ($calls as $call) {
            $price += $call->getPrice() ?: 0;
            ++$count;
        }

        $averageBill = (float)($count > 0 ? $price / $count : 0);

        return $this->json([
            'value' => (int)$averageBill,
            'rates' => json_decode($payrollCalculator->getValue(), true),
            'rate' => number_format(
                $payrollCalculator->getRate($averageBill),
                1, '.', ''
            ),
        ]);
    }

}
