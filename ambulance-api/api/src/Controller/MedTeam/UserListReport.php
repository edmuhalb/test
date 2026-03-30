<?php

declare(strict_types=1);

namespace App\Controller\MedTeam;

use App\Entity\MedTeam\MedTeam;
use App\Repository\MedTeam\MedTeamRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserListReport extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository $teams,
    ) {}

    /**
     * @throws Exception
     */
    #[Route(path: '/api/med_teams/report-user-list', name: 'med_teams_report_user_list', methods: 'GET', priority: 10)]
    public function __invoke(Request $request): JsonResponse
    {
        $start = $request->query->get('start');
        $end = $request->query->get('end');

        if (!$start || !$end) {
            return $this->json([]);
        }

        $teams = $this->teams->findForReportByPlanned(
            (new DateTimeImmutable($start))->setTime(0, 0),
            (new DateTimeImmutable($end))->setTime(23, 59, 59)
        );

        $result = [];

        /** @var MedTeam $team */
        foreach ($teams as $team) {
            if ($team->getAdmin()) {
                $result[] = [
                    'data' => $team->getPlannedStartAt()->format('d.m.y'),
                    'team' => $team->getPhone()?->getId() ?: '',
                    'start' => $team->getPlannedStartAt()?->format('H:i') ?: '',
                    'end' => $team->getPlannedFinishAt()?->format('H:i') ?: '',
                    'car' => $team->getCar()?->getName() ?: '',
                    'position' => 'админ',
                    'name' => $team->getAdmin()->getName(),
                ];
            }
            if ($team->getDoctor()) {
                $result[] = [
                    'data' => $team->getPlannedStartAt()->format('d.m.y'),
                    'team' => $team->getPhone()?->getId() ?: '',
                    'start' => $team->getPlannedStartAt()?->format('H:i') ?: '',
                    'end' => $team->getPlannedFinishAt()?->format('H:i') ?: '',
                    'car' => $team->getCar()?->getName() ?: '',
                    'position' => 'врач',
                    'name' => $team->getDoctor()->getName(),
                ];
            }
            if ($team->getDriver()) {
                $result[] = [
                    'data' => $team->getPlannedStartAt()->format('d.m.y'),
                    'team' => $team->getPhone()?->getId() ?: '',
                    'start' => $team->getPlannedStartAt()?->format('H:i') ?: '',
                    'end' => $team->getPlannedFinishAt()?->format('H:i') ?: '',
                    'car' => $team->getCar()?->getName() ?: '',
                    'position' => 'водитель',
                    'name' => $team->getDriver()->getName(),
                ];
            }
        }

        return $this->json($result);
    }
}
