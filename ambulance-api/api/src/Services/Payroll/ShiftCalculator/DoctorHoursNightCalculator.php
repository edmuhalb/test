<?php

declare(strict_types=1);

namespace App\Services\Payroll\ShiftCalculator;

use App\Entity\MedTeam\MedTeam;
use App\Entity\User\User;

class DoctorHoursNightCalculator extends AbstractEmployeeCalculator
{
    protected function getHoursKey(): string
    {
        return 'night';
    }

    protected function getEmployee(MedTeam $shift): ?User
    {
        return $shift->getDoctor();
    }
}
