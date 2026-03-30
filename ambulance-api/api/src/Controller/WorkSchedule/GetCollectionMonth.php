<?php

declare(strict_types=1);

namespace App\Controller\WorkSchedule;

use App\Entity\User\User;
use App\Entity\WorkSchedule;
use App\Repository\UserRepository;
use App\Repository\WorkScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetCollectionMonth extends AbstractController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly WorkScheduleRepository $workSchedules,
    ) {}

    #[Route(path: '/api/work_schedules/month/{role}/{year}/{month}', name: 'work_schedule_month', methods: 'GET')]
    public function __invoke(string $role, int $year, int $month, Request $request): JsonResponse
    {
        $permission = $role;
        if ($role === 'ROLE_ADMIN') {
            $permission = 'can_be-admin';
        } elseif ($role === 'ROLE_DOCTOR') {
            $permission = 'can_be-doctor';
        } elseif ($role === 'ROLE_DRIVER') {
            $permission = 'can_be-driver';
        } elseif ($role === 'ROLE_SMP_DRIVER') {
            $permission = 'can_be-smp_driver';
        } elseif ($role === 'ROLE_SMP_REANIMATOR') {
            $permission = 'can_be-cmp_reanimator';
        } elseif ($role === 'ROLE_SMP_PARAMEDIC') {
            $permission = 'can_be-smp_paramedic';
        } elseif ($role === 'ROLE_SMP_DOCTOR') {
            $permission = 'can_be-smp_doctor';
        }

        $cityId =  $request->get('city') ? (int)$request->get('city') : null;

        $users = $this->users->findAllByPermission($permission, $cityId);

        $cityId =  $request->get('city') ? (int)$request->get('city') : null;

        $workSchedules = $this->workSchedules->findAllByRole($role, $year, $month, $cityId);

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

        $result = [
            'total' => $totalDays,
            'users' => [],
        ];

        /** @var User $user */
        foreach ($users as $user) {
            $result['users'][$user->getName()] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'schedules' => $daysArray,
                'total' =>$totalTemplate,
            ];
        }

        /** @var WorkSchedule $workSchedule */
        foreach ($workSchedules as $workSchedule) {
            $employeeName = $workSchedule->getEmployee()->getName();

            if (!\array_key_exists($employeeName, $result['users'])) {
                $result['users'][$employeeName] = [
                    'id' => $workSchedule->getEmployee()->getId(),
                    'name' => $employeeName,
                    'schedules' => $daysArray,
                    'total' =>$totalTemplate,
                ];
            }

            $result['users'][$employeeName]['schedules'][$workSchedule->getDay()] = [
                'id' => $workSchedule->getId(),
                'workDate' => $workSchedule->getWorkDate(),
                'employee' => $workSchedule->getEmployee()->getId(),
                'type' => $workSchedule->getType(),
                'role' => $workSchedule->getRole(),
            ];
            ++$result['users'][$employeeName]['total'][$workSchedule->getType()];

            ++$result['total'][$workSchedule->getDay()][$workSchedule->getType()];
        }

        return $this->json($result);
    }
}
