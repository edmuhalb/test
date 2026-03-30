<?php

declare(strict_types=1);

namespace App\Controller\Partner;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\Hospital\Hospital;
use App\Query\PartnerReward\Fetcher;
use App\Query\PartnerReward\Query;
use App\Repository\CallingRepository;
use App\Repository\Hospital\HospitalRepository;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CallsReport extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls,
        private readonly HospitalRepository $hospitals,
        private readonly Fetcher $partnerRewardFetcher,
    ) {}

    /**
     * @throws Exception
     */
    #[Route(path: '/api/calls/report', name: 'partner_calls_report', methods: 'GET', priority: 10)]
    public function __invoke(Request $request): JsonResponse
    {
        $completedAtAfter = $request->query->get('completedAtAfter');
        $completedAtBefore = $request->query->get('completedAtBefore');

        $period = new DatePeriod(
            new DateTimeImmutable($completedAtBefore),
            new DateInterval('P1D'),
            new DateTimeImmutable($completedAtAfter)
        );

        $calls = $this->calls->findAllByCompletedAtFromPeriod($period);

        $partners = [];

        $debit = 0;
        $callEntranceCash = 0;
        $callEntranceCashless = 0;
        $callEntrance = 0;
        $callAccrued = 0;
        $hospitalEntranceCash = 0;
        $hospitalEntranceCashless = 0;
        $hospitalEntrance = 0;
        $hospitalAccrued = 0;
        $stationaryEntrance = 0;
        $stationaryAccrued = 0;

        /** @var Calling $call */
        foreach ($calls as $call) {
            if (!$call?->getPartner()?->getId()) {
                continue;
            }
            $id = 'call-' . $call->getId();
            if ($call->getStatus() !== Status::COMPLETED) {
                continue;
            }

            if (!\array_key_exists($call->getPartner()->getId(), $partners)) {
                $partners[$call->getPartner()->getId()] = [
                    'id' => $call->getPartner()->getId(),
                    'name' => $call->getPartner()->getName(),
                    'isOur' => $call->getPartner()->isOur(),
                    'calls' => [],
                    'debit' => 0,
                    'callEntranceCash' => 0,
                    'callEntranceCashless' => 0,
                    'callEntrance' => 0,
                    'callAccrued' => 0,
                    'hospitalEntranceCash' => 0,
                    'hospitalEntranceCashless' => 0,
                    'hospitalEntrance' => 0,
                    'hospitalAccrued' => 0,
                    'stationaryEntrance' => 0,
                    'stationaryAccrued' => 0,
                ];
            }

            $partners[$call->getPartner()->getId()]['calls'][$id] = [
                'id' => $call->getId(),
                'month' => $call->getFinishedAt() ? (int)$call->getFinishedAt()->format('m') : '',
                'day' => $call->getFinishedAt() ? (int)$call->getFinishedAt()->format('d') : '',
                'external' => $call->getNumberCalling(),
                'city' => $call->getCity()?->getName(),
                'name' => $call->getFio(),
                'debit' => 0,
                'callEntranceCash' => 0,
                'callEntranceCashless' => 0,
                'callEntrance' => 0,
                'callAccrued' => 0,
                'hospitalEntranceCash' => 0,
                'hospitalEntranceCashless' => 0,
                'hospitalEntrance' => 0,
                'hospitalAccrued' => 0,
                'clinic' => '',
                'stationaryEntrance' => 0,
                'stationaryAccrued' => 0,
                'hospitalized' => '',
                'discharged' => '',
                'admin' => $call->getTeam()?->getAdmin()?->getName(),
                'doctor' => $call->getTeam()?->getDoctor()?->getName(),
                'repeat' => $call->getCountRepeat(),
            ];

            foreach ($call->getServices() as $service) {
                if ($service->isHospital()) {
                    $partners[$call->getPartner()->getId()]['calls'][$id]['hospitalEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['calls'][$id]['hospitalAccrued']
                        += $service->getPartnerReward();

                    $partners[$call->getPartner()->getId()]['hospitalEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['hospitalAccrued']
                        += $service->getPartnerReward();
                    if ($service->getInCash()){
                        $hospitalEntranceCash += $service->getPrice();
                        $partners[$call->getPartner()->getId()]['calls'][$id]['hospitalEntranceCash']
                            += $service->getPrice();
                        $partners[$call->getPartner()->getId()]['hospitalEntranceCash']
                            += $service->getPrice();
                    }else{
                        $hospitalEntranceCashless += $service->getPrice();
                        $partners[$call->getPartner()->getId()]['calls'][$id]['hospitalEntranceCashless']
                            += $service->getPrice();
                        $partners[$call->getPartner()->getId()]['hospitalEntranceCashless']
                            += $service->getPrice();
                    }
                    $hospitalEntrance  += $service->getPrice();
                    $hospitalAccrued += $service->getPartnerReward();
                } elseif ($service->isTherapy()) {
                    $partners[$call->getPartner()->getId()]['calls'][$id]['callEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['calls'][$id]['callAccrued']
                        += $service->getPartnerReward();

                    $partners[$call->getPartner()->getId()]['callEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['callAccrued']
                        += $service->getPartnerReward();

                    $callEntrance  += $service->getPrice();
                    $callAccrued += $service->getPartnerReward();
                    if ($service->getInCash()){
                        $callEntranceCash += $service->getPrice();
                        $partners[$call->getPartner()->getId()]['calls'][$id]['callEntranceCash']
                            += $service->getPrice();
                        $partners[$call->getPartner()->getId()]['callEntranceCash']
                            += $service->getPrice();
                    }else{
                        $callEntranceCashless += $service->getPrice();
                        $partners[$call->getPartner()->getId()]['calls'][$id]['callEntranceCashless']
                            += $service->getPrice();
                        $partners[$call->getPartner()->getId()]['callEntranceCashless']
                            += $service->getPrice();
                    }
                }

                $partners[$call->getPartner()->getId()]['calls'][$id]['debit'] = $call->getPrice();
                $partners[$call->getPartner()->getId()]['debit'] += $call->getPrice();
                $debit += $call->getPrice();
            }
        }

        $hospitals = $this->hospitals->findAllByDischargedAtFromPeriod($period);

        /** @var Hospital $hospital */
        foreach ($hospitals as $hospital) {
            $id = 'hospital-' . $hospital->getId();
            if (!\array_key_exists($hospital->getPartner()->getId(), $partners)) {
                $partners[$hospital->getPartner()->getId()] = [
                    'id' => $hospital->getPartner()->getId(),
                    'name' => $hospital->getPartner()->getName(),
                    'isOur' => $hospital->getPartner()->isOur(),
                    'calls' => [],
                    'debit' => 0,
                    'callEntranceCash' => 0,
                    'callEntranceCashless' => 0,
                    'callEntrance' => 0,
                    'callAccrued' => 0,
                    'hospitalEntranceCash' => 0,
                    'hospitalEntranceCashless' => 0,
                    'hospitalEntrance' => 0,
                    'hospitalAccrued' => 0,
                    'stationaryEntrance' => 0,
                    'stationaryAccrued' => 0,
                ];
            }
            $query = new Query(
                $hospital->getDischarged(),
                $hospital->getPartner()->getId(),
                2,
                0,
                0
            );

            $rewardPercent = $this->partnerRewardFetcher->fetch($query);
            $reward = $hospital->getMainAmount() / 100 * $rewardPercent;

            $partners[$hospital->getPartner()->getId()]['calls'][$id] = [
                'id' => $hospital->getId(),
                'external' => $hospital->getExternal(),
                'name' => $hospital->getFio(),
                'debit' => 0,
                'callEntranceCash' => 0,
                'callEntranceCashless' => 0,
                'callEntrance' => 0,
                'callAccrued' => 0,
                'hospitalEntrance' => 0,
                'hospitalAccrued' => 0,
                'clinic' => $hospital->getClinic()->getName(),
                'stationaryEntrance' => $hospital->getAmount(),
                'stationaryAccrued' => $reward,
                'hospitalized' => $hospital->getHospitalized()?->format('d.m.Y'),
                'discharged' => $hospital->getDischarged()?->format('d.m.Y'),
                'admin' => $call->getTeam()?->getAdmin()?->getName(),
                'doctor' => $call->getTeam()?->getDoctor()?->getName(),
                'repeat' => $call->getCountRepeat(),
            ];

            $partners[$hospital->getPartner()->getId()]['stationaryEntrance']
                += $hospital->getAmount();
            $partners[$hospital->getPartner()->getId()]['stationaryAccrued']
                += $reward;

            $stationaryEntrance  += $hospital->getAmount();
            $stationaryAccrued += $reward;

            $partners[$hospital->getPartner()->getId()]['calls'][$id]['debit'] = $hospital->getAmount();
            $partners[$hospital->getPartner()->getId()]['debit'] += $hospital->getAmount();
            $debit += $hospital->getAmount();
        }

        return $this->json([
            'items' => $partners,
            'debit' => $debit,
            'callEntranceCash' => $callEntranceCash,
            'callEntranceCashless' => $callEntranceCashless,
            'callEntrance' => $callEntrance,
            'callAccrued' => $callAccrued,
            'hospitalEntranceCash' => $hospitalEntranceCash,
            'hospitalEntranceCashless' => $hospitalEntranceCashless,
            'hospitalEntrance' => $hospitalEntrance,
            'hospitalAccrued' => $hospitalAccrued,
            'stationaryEntrance' => $stationaryEntrance,
            'stationaryAccrued' => $stationaryAccrued,
        ]);
    }
}
