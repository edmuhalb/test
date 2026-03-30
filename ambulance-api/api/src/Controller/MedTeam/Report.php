<?php

declare(strict_types=1);

namespace App\Controller\MedTeam;

use App\Entity\MedTeam\MedTeam;
use App\Entity\User\User;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Report extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository $medTeams,
        private readonly UserRepository $users
    ) {}

    /**
     * @throws Exception
     */
    #[Route(path: '/api/med_teams/report', name: 'med_teams_report', methods: 'GET', priority: 10)]
    public function __invoke(Request $request): JsonResponse
    {
        $startDate = $request->query->get('startDate');
        $endDate= $request->query->get('endDate');

        $startDate  =new DateTimeImmutable($startDate);
        $month = (int)$startDate->format('m');
        $year = (int)$startDate->format('Y');

        $medTeams = $this->medTeams->findByPlanned(
            $startDate,
            new DateTimeImmutable($endDate),
        );

        $users = $this->users->findAllByRole('ROLE_ADMIN');

        $numDays = date('t', mktime(0, 0, 0, $month, 1, $year));

        $daysArray = [];
        $totalDays = [];
        $totalTemplate = [
            'daytime' => 0,
            'night' => 0,
            'evening' => 0,
            'day' => 0,
            'stop' => 0,
        ];

        for ($i = 1; $i <= $numDays; ++$i) {
            $daysArray[$i] = null;
            $totalDays[$i] = $totalTemplate;
        }

        $resultAdmin = [
            'total' => $totalDays,
            'users' => [],
        ];

        /** @var User $user */
        foreach ($users as $user) {
            $resultAdmin['users'][$user->getName()] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'clocks' => $daysArray,
                'total' =>$totalTemplate,
            ];
        }

        /** @var MedTeam $medTeam */
        foreach ($medTeams as $medTeam) {
            $employeeName = $medTeam->getAdmin()->getName();
            if (!\array_key_exists($employeeName, $resultAdmin['users'])) {
                $resultAdmin['users'][$employeeName] = [
                    'id' => $medTeam->getAdmin()->getId(),
                    'name' => $employeeName,
                    'clocks' => $daysArray,
                    'total' =>$totalTemplate,
                ];
            }

            $resultAdmin['users'][$employeeName]['clocks'][$medTeam->getDay()] = $medTeam->getPlannedHours();
            ++$resultAdmin['users'][$employeeName]['total'][$medTeam->getType()];

            ++$resultAdmin['total'][$medTeam->getDay()][$medTeam->getType()];
        }

        $users = $this->users->findAllByRole('ROLE_DOCTOR');

        $resultDoctor = [
            'total' => $totalDays,
            'users' => [],
        ];

        /** @var User $user */
        foreach ($users as $user) {
            $resultDoctor['users'][$user->getName()] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'clocks' => $daysArray,
                'total' =>$totalTemplate,
            ];
        }

        /** @var MedTeam $medTeam */
        foreach ($medTeams as $medTeam) {
            $employeeName = $medTeam->getDoctor()->getName();
            if (!\array_key_exists($employeeName, $resultDoctor['users'])) {
                $resultDoctor['users'][$employeeName] = [
                    'id' => $medTeam->getDoctor()->getId(),
                    'name' => $employeeName,
                    'clocks' => $daysArray,
                    'total' =>$totalTemplate,
                ];
            }

            $resultDoctor['users'][$employeeName]['clocks'][$medTeam->getDay()] = $medTeam->getPlannedHours();
            ++$resultDoctor['users'][$employeeName]['total'][$medTeam->getType()];

            ++$resultDoctor['total'][$medTeam->getDay()][$medTeam->getType()];
        }

        $users = $this->users->findAllByRole('ROLE_DRIVER');

        $resultDriver = [
            'total' => $totalDays,
            'users' => [],
        ];

        /** @var User $user */
        foreach ($users as $user) {
            $resultDriver['users'][$user->getName()] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'clocks' => $daysArray,
                'total' =>$totalTemplate,
            ];
        }

        /** @var MedTeam $medTeam */
        foreach ($medTeams as $medTeam) {
            if (!$medTeam->getDriver()) {
                continue;
            }

            $employeeName = $medTeam->getDriver()->getName();
            if (!\array_key_exists($employeeName, $resultDriver['users'])) {
                $resultDriver['users'][$employeeName] = [
                    'id' => $medTeam->getDriver()->getId(),
                    'name' => $employeeName,
                    'clocks' => $daysArray,
                    'total' =>$totalTemplate,
                ];
            }

            $resultDriver['users'][$employeeName]['clocks'][$medTeam->getDay()] = $medTeam->getPlannedHours();
            ++$resultDriver['users'][$employeeName]['total'][$medTeam->getType()];

            ++$resultDriver['total'][$medTeam->getDay()][$medTeam->getType()];
        }

        return $this->json(
            [
                'admins' => $resultAdmin,
                'doctors' => $resultDoctor,
                'drivers' => $resultDriver,
            ]
        );
    }
}
