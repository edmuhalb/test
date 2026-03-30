<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use WebSocket\Client;

class WSClient
{
    public function __construct(
        private readonly string $url
    ) {}

    public function send(string $message): void
    {
        $client = new Client($this->url);

        $client->text($message);

        $client->close();
    }

    public function sendUpdateOffer(?int $id): void
    {
        try {
            $this->send(
                json_encode([
                    'event' => 'calls_updated',
                    'data' => [
                        'id' => $id,
                    ],
                ])
            );
        } catch (Exception) {
        }
    }

    public function sendAddedToQueue(?int $id): void
    {
        try {
            $this->send(
                json_encode([
                    'event' => 'queue_calls_added',
                    'data' => [
                        'id' => $id,
                    ],
                ])
            );
        } catch (Exception) {
        }
    }

    public function sendUpdateTeam(?int $id): void
    {
        try {
            $this->send(
                json_encode([
                    'event' => 'teams_updated',
                    'data' => [
                        'id' => $id,
                    ],
                ])
            );
        } catch (Exception) {
        }
    }
}
