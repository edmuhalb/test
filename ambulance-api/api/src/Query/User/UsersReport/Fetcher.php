<?php

declare(strict_types=1);

namespace App\Query\User\UsersReport;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\Hospital\Hospital;
use App\Entity\MedTeam\MedTeam;
use App\Entity\User\User;
use App\Repository\Hospital\HospitalRepository;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\UserRepository;
use App\Services\PeriodService\PeriodService;
use Exception;

readonly class Fetcher
{
    public function __construct(
        private PeriodService $periodService,
        private HospitalRepository $hospitals,
        private MedTeamRepository $teams,
        private UserRepository $users,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(Query $query): array
    {
        $period = $this->periodService->createDatePeriodFromRequest($query->period);
        $hospitals = $this->hospitals->findAllCompletedByHospitalizedAtFromPeriod($period);
        $teams = $this->teams->findByPlanned($period->getStartDate(), $period->getEndDate());

        $sort = $query->sort;
        $order = $query->order;

        $users = $this->users->findAllByPermissions([
            'can_be-admin',
            'can_be-doctor',
        ]);

        $result = [];

        /** @var User $user */
        foreach ($users as $user) {
            $result[$user->getId()] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'permissions' => $user->getPermissions(),

                'revenue' => 0,                     // Выручка ВСЕГО
                'salary' => 'n/a',                  // ЗП ВСЕГО

                'workShiftCount' => 0,              // Смены ВСЕГО шт
                'workShiftHours' => 0,              // Смены ВСЕГО часы
                'workShiftSalary' => 'n/a',        // ЗП Смены ВСЕГО

                'dutyCount' => 0,                   // Дежурства в смену на мероприятиях
                'dutyHours' => 0,                   // Дежурства в смену на мероприятиях
                'dutySalary' => 'n/a',              // ЗП Дежурства в смену на мероприятиях

                'callsRevenue' => 0,                   // Выручка Выезды ВСЕГО
                'callsCancelledCount' => 0,
                'callsCount' => 0,
                'callsAverageCheck' => 0,
                'callsSalary' => 'n/a',

                'callsPrimaryRevenue' => 0,             // Выручка выезды первичные
                'callsPrimaryCount' => 0,
                'callsPrimaryAverageCheck' => 0,
                'callsPrimarySalary' => 'n/a',

                'callsRepeatRevenue' => 0,          // Выручка выезды повторы
                'callsRepeatCount' => 0,
                'callsRepeatAverageCheck' => 0,
                'callsRepeatSalary' => 'n/a',

                'codingRevenue' => 0,               // Кодирование выручка
                'codingCount' => 0,                 // Кодирование количество
                'codingSalary' => 'n/a',            // Кодирование зарплата

                'hospitalCount' => 0,            // Количество госпитализаций
                'stationaryCount' => 0,            // Количество стационаров
            ];
        }

        /** @var MedTeam $team */
        foreach ($teams as $team) {
            /** @var Calling $call */
            foreach ($team->getCallings() as $call) {
                $adminId = $call?->getAdmin()?->getId();
                $result = $this->getArr($result, $adminId, $call);

                $doctorId = $call?->getDoctor()?->getId();
                if ($doctorId !== $adminId) {
                    $result = $this->getArr($result, $doctorId, $call);
                }
            }
        }

        /** @var MedTeam $team */
        foreach ($teams as $team) {
            $hours = $team->getPlannedHours();
            $dutyHours = $team->getDutyHours();
            $adminId = $team->getAdmin()->getId();
            if (\array_key_exists($adminId, $result)) {
                ++$result[$adminId]['workShiftCount'];
                $result[$adminId]['workShiftHours'] += $hours;
                if ($dutyHours > 0) {
                    ++$result[$adminId]['dutyCount'];
                    $result[$adminId]['dutyHours'] += $dutyHours;
                }
            }
            $doctorId = $team->getDoctor()->getId();
            if ($doctorId !== $adminId && \array_key_exists($doctorId, $result)) {
                ++$result[$doctorId]['workShiftCount'];
                $result[$doctorId]['workShiftHours'] += $hours;
                if ($dutyHours > 0) {
                    ++$result[$doctorId]['dutyCount'];
                    $result[$doctorId]['dutyHours'] += $dutyHours;
                }
            }
        }

        /** @var Hospital $hospital */
        foreach ($hospitals as $hospital) {
            if ($hospital->getOwner()) {
                $call = $hospital->getOwner();
                $adminId = $call?->getAdmin()?->getId();

                if (\array_key_exists($adminId, $result)) {
                    ++$result[$adminId]['stationaryCount'];
                }

                $doctorId = $call?->getDoctor()?->getId();
                if ($doctorId !== $adminId) {
                    if (\array_key_exists($doctorId, $result)) {
                        ++$result[$doctorId]['stationaryCount'];
                    }
                }
            }
        }

        usort($result, static function ($item1, $item2) use ($sort, $order) {
            if ($order === 'desc') {
                return $item2[$sort] <=> $item1[$sort];
            }
            return $item1[$sort] <=> $item2[$sort];
        });

        return $result;
    }

    public function getArr(array $result, ?int $adminId, Calling $call): array
    {
        if (!\array_key_exists($adminId, $result)) {
            return $result;
        }

        if ($call->getStatus() === Status::REJECTED) {
            ++$result[$adminId]['callsCancelledCount'];
            return $result;
        }

        $result[$adminId]['revenue'] += $call->getPrice();

        // вызовы всего
        ++$result[$adminId]['callsCount'];
        $result[$adminId]['callsRevenue'] += $call->getPrice();
        if ($result[$adminId]['callsCount'] > 0) {
            $result[$adminId]['callsAverageCheck']
                = (int)($result[$adminId]['callsRevenue'] / $result[$adminId]['callsCount']);
        }

        if ($call->getCountRepeat() === 0) {
            ++$result[$adminId]['callsPrimaryCount'];
            $result[$adminId]['callsPrimaryRevenue'] += $call->getPrice();
            if ($result[$adminId]['callsPrimaryCount'] > 0) {
                $result[$adminId]['callsPrimaryAverageCheck']
                    = (int)($result[$adminId]['callsPrimaryRevenue'] / $result[$adminId]['callsPrimaryCount']);
            }
        } else { // вторичные вызовы
            ++$result[$adminId]['callsRepeatCount'];
            $result[$adminId]['callsRepeatRevenue'] += $call->getPrice();
            if ($result[$adminId]['callsRepeatCount'] > 0) {
                $result[$adminId]['callsRepeatAverageCheck']
                    = (int)($result[$adminId]['callsRepeatRevenue'] / $result[$adminId]['callsRepeatCount']);
            }
        }

        foreach ($call->getServices() as $service) {
            if ($service->getService()?->getCategory()?->getId() === 3) {
                ++$result[$adminId]['codingCount'];
                $result[$adminId]['codingRevenue'] += $service->getPrice();
            } elseif ($service->getService()->getType() === 'hospital') {
                ++$result[$adminId]['hospitalCount'];
            }
            break;
        }

        return $result;
    }
}
