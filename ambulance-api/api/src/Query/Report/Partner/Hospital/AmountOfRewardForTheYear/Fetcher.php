<?php

declare(strict_types=1);

namespace App\Query\Report\Partner\Hospital\AmountOfRewardForTheYear;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class Fetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(Query $query): array
    {
        $sql = 'SELECT SUM(h.partner_reward) AS amount, MONTH(h.discharged_at) as month, YEAR(h.discharged_at) as year
            FROM hospital_hospitals h
            WHERE h.discharged_at BETWEEN :start_date AND :end_date
            AND h.partner_id = :partner_id
            GROUP BY month
            ORDER BY year, month';

        $startDate = (new DateTimeImmutable($query->startDate))->format('Y-m-d');
        $endDate = (new DateTimeImmutable($query->endDate))->format('Y-m-d');

        $statement = $this->connection->executeQuery($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'partner_id' => $query->partnerId,
        ]);

        return $statement->fetchAllAssociative();
    }
}
