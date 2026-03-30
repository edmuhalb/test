<?php

declare(strict_types=1);

namespace App\Services\PeriodService;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Exception;

class PeriodService
{
    /**
     * @throws Exception
     */
    public function createDatePeriodFromRequest(string $period): DatePeriod
    {
        if ($period === 'today') {
            $start = new DateTimeImmutable('midnight today');
            $end = new DateTimeImmutable('tomorrow midnight');
        } elseif ($period === 'yesterday') {
            $start = new DateTimeImmutable('yesterday midnight');
            $end =  new DateTimeImmutable('midnight today');
        } elseif ($period === 'week') {
            $now = new DateTimeImmutable('now midnight');
            $start = $now->setISODate((int)$now->format('o'), (int)$now->format('W'), 1);
            $end = $start->modify('+7 days');
        } elseif ($period === 'month') {
            $now = new DateTimeImmutable('now');
            $start = $now->modify('first day of this month midnight');
            $end = $now->modify('first day of next month midnight');
        } elseif ($period === 'quarter') {
            $now = new DateTimeImmutable('midnight');

            $month = (int)$now->format('n');

            $startMonth = $month - ($month - 1) % 3;
            $endMonth = $startMonth + 2;

            $start = $now->setDate((int)$now->format('Y'), $startMonth, 1);
            $end = $endMonth === 12
                ? $now->setDate((int)$now->format('Y') + 1, $endMonth + 1, 1)
                : $now->setDate((int)$now->format('Y'), $endMonth + 1, 1);
        } else {
            $dates = explode(':', $period);

            $start = new DateTimeImmutable($dates[0] . ' midnight');
            $end = (new DateTimeImmutable($dates[1] . ' midnight'))->modify('+1 days');
        }

        $interval = new DateInterval('P1D');

        return new DatePeriod($start, $interval, $end);
    }
}
