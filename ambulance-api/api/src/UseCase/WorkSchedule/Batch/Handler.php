<?php

declare(strict_types=1);

namespace App\UseCase\WorkSchedule\Batch;

use App\Entity\WorkSchedule;
use App\Repository\UserRepository;
use App\Repository\WorkScheduleRepository;

class Handler
{
    public function __construct(
        private readonly WorkScheduleRepository $schedules,
        private readonly UserRepository $users,
    ) {}

    public function handle(Command $command): void
    {
        $user = $this->users->get($command->user);

        $schedules = $this->schedules->findAllByUserAndDates(
            $command->user,
            $command->dateStart,
            $command->dateEnd,
            $command->city
        );

        foreach ($schedules as $schedule) {
            $this->schedules->remove($schedule, true);
        }

        if ($command->type === 'clear') {
            return;
        }

        $dateList = [];
        $tempDate = $command->dateStart;

        while ($tempDate <= $command->dateEnd) {
            $dateList[] = $tempDate;
            $tempDate = $tempDate->modify('+1 day');
        }

        foreach ($dateList as $date) {
            $schedule = new WorkSchedule();
            $schedule->setEmployee($user);
            $schedule->setRole($command->role);
            $schedule->setType($command->type);
            $schedule->setWorkDate($date);

            $this->schedules->save($schedule, true);
        }
    }
}
