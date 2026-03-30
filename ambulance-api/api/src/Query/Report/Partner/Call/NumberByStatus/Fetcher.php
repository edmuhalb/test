<?php

declare(strict_types=1);

namespace App\Query\Report\Partner\Call\NumberByStatus;

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
        $sql = 'SELECT COUNT(c.id) AS count, c.status as status
            FROM calling c
            WHERE c.partner_id = :partner_id
            GROUP BY c.status';

        $statement = $this->connection->executeQuery($sql, [
            'partner_id' => $query->partnerId,
        ]);

        return $statement->fetchAllAssociative();
    }
}
