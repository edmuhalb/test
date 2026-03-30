<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\Calling\Calling;
use App\Repository\CallingRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/my/average-bill-for-year', name: 'api_my_average_bill_for_year', methods: ['GET'])]
class AverageBillForYearAction extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls,
    ) {}

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $userId = $this->getUser()?->getId();
        if (!$userId) {
            throw new \DomainException('User not found');
        }

        $now = new DateTimeImmutable();
        $startOfYear = $now
            ->modify('-1 year')
            ->modify('+1 month')
            ->modify('first day of this month 00:00:00');

        $monthlyAverages = [];

        for ($i = 0; $i < 12; $i++) {
            $startOfMonth = $startOfYear->modify("+$i month");
            $endOfMonth = $startOfMonth->modify('first day of next month 00:00:00');

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

            $monthlyAverages[] = (int)($count > 0 ? $price / $count : 0);
        }

        return $this->json([
            'values' => $monthlyAverages,
        ]);
    }
}
