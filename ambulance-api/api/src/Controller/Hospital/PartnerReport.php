<?php

declare(strict_types=1);

namespace App\Controller\Hospital;

use App\Entity\Hospital\Hospital;
use App\Query\PartnerReward\Fetcher;
use App\Query\PartnerReward\Query;
use App\Repository\Hospital\HospitalRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PartnerReport extends AbstractController
{
    public function __construct(
        private readonly Fetcher $partnerRewardFetcher,
        private readonly HospitalRepository $hospitals
    ) {}

    /**
     * @throws Exception
     */
    #[Route(path: '/api/hospitals/report', name: 'hospitals_report', methods: 'GET', priority: 10)]
    public function __invoke(Request $request): JsonResponse
    {
        $partnerId = $request->query->get('partnerId');

        $dischargedAtAfter = $request->query->get('dischargedAtAfter');
        $dischargedAtBefore = $request->query->get('dischargedAtBefore');

        $hospitals = $this->hospitals->findByPartnerAndDischargedAt(
            (int)$partnerId,
            new DateTimeImmutable($dischargedAtAfter),
            new DateTimeImmutable($dischargedAtBefore),
        );

        $items = [];
        $totalAmount = 0;
        $totalReward = 0;

        /** @var Hospital $hospital */
        foreach ($hospitals as $hospital) {
            $query = new Query(
                $hospital->getDischarged(),
                $hospital->getPartner()->getId(),
                2,
                0,
                0
            );

            $rewardPercent = $this->partnerRewardFetcher->fetch($query);
            $reward = $hospital->getMainAmount() / 100 * $rewardPercent;

            $totalAmount += $hospital->getMainAmount();
            $totalReward += $reward;

            $items[] = [
                'hospitalized' => $hospital->getHospitalized()?->format('d.m.Y'),
                'discharged' => $hospital->getDischarged()?->format('d.m.Y'),
                'fio' => $hospital->getFio(),
                'phone' => $hospital->getPhone(),
                'amount' => $hospital->getMainAmount(),
                'reward' => $reward,
            ];
        }

        $hospitals = $this->hospitals->findByPartnerAndHospitalizedAt(
            (int)$partnerId,
            new DateTimeImmutable($dischargedAtAfter),
            new DateTimeImmutable($dischargedAtBefore),
        );

        /** @var Hospital $hospital */
        foreach ($hospitals as $hospital) {
            $items[] = [
                'hospitalized' => $hospital->getHospitalized()?->format('d.m.Y'),
                'discharged' => 'Не выписан',
                'fio' => $hospital->getFio(),
                'phone' => $hospital->getPhone(),
                'amount' => 0,
                'reward' => 0,
            ];
        }

        return $this->json([
            'items' => $items,
            'amount' => $totalAmount,
            'reward' => $totalReward,
        ]);
    }
}
