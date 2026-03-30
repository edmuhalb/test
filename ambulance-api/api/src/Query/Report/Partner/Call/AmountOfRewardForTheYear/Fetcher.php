<?php

declare(strict_types=1);

namespace App\Query\Report\Partner\Call\AmountOfRewardForTheYear;

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
        $sql = 'SELECT SUM(c.partner_reward) AS amount, MONTH(c.completed_at) as month, YEAR(c.completed_at) as year
            FROM calling c
            WHERE c.completed_at BETWEEN :start_date AND :end_date
            AND c.partner_id = :partner_id
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
