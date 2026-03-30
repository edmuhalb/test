<?php

declare(strict_types=1);

namespace App\Asterisk\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

readonly class ChannelRepository
{
    public function __construct(
        private ManagerRegistry $doctrine
    ) {}

    public function hasChannelByClientPhoneNumber(string $clientPhoneNumber): bool
    {
        /** @var Connection $connection */
        $connection = $this->doctrine->getConnection('asterisk');
        return (bool)$connection->fetchOne(
            'SELECT 1 FROM channels WHERE client = :clientPhoneNumber',
            ['clientPhoneNumber' => $clientPhoneNumber]
        );
    }

    public function create(string $clientPhoneNumber, string $teamNumber): void
    {
        /** @var Connection $connection */
        $connection = $this->doctrine->getConnection('asterisk');
        $connection->insert('channels', ['client' => $clientPhoneNumber, 'team' => $teamNumber]);
    }

    public function update(string $clientPhoneNumber, string $teamNumber): void
    {
        /** @var Connection $connection */
        $connection = $this->doctrine->getConnection('asterisk');
        $connection->update('channels', ['team' => $teamNumber], ['client' => $clientPhoneNumber]);
    }

    public function deleteChannelByClientPhoneNumber(string $clientPhoneNumber): void
    {
        /** @var Connection $connection */
        $connection = $this->doctrine->getConnection('asterisk');
        $connection->delete('channels', ['client' => $clientPhoneNumber]);
    }
}
