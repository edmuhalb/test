<?php

declare(strict_types=1);

namespace App\Services\Payroll\ShiftCalculator;

use DomainException;

class ShiftCalculatorStrategy
{
    private array $strategies;

    public function __construct(
        AdminHoursDayCalculator $adminHoursDay,
        AdminHoursNightCalculator $adminHoursNight,
        DoctorHoursDayCalculator $doctorHoursDay,
        DoctorHoursNightCalculator $doctorHoursNight,
        FuelCalculator $fuel,
        ParkingCalculator $parking,
        RentCarCalculator $rentCar
    ) {
        $this->strategies = [
            'shift_admin_hours_day' => $adminHoursDay,
            'shift_admin_hours_night' => $adminHoursNight,
            'shift_doctor_hours_day' => $doctorHoursDay,
            'shift_doctor_hours_night' => $doctorHoursNight,
            'shift_fuel' => $fuel,
            'shift_parking' => $parking,
            'shift_rent_car' => $rentCar,
        ];
    }

    public function getStrategy($processor): ShiftCalculatorInterface
    {
        if (!isset($this->strategies[$processor])) {
            throw new DomainException(
                'Unknown payroll shift strategy ' .
                $processor
            );
        }
        return $this->strategies[$processor];
    }
}
