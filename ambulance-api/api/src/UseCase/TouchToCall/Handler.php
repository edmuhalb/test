<?php

declare(strict_types=1);

namespace App\UseCase\TouchToCall;

use App\Repository\CallingRepository;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class Handler
{
    public function __construct(
        private CallingRepository $calls,
        private HttpClientInterface $client,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function handle(Command $command): void
    {
        $call = $this->calls->getById($command->callId);

        $clientPhone = preg_replace('/[^0-9]/', '', $call->getClient()?->getPhone());

        $adminPhone = preg_replace('/[^0-9]/', '', $call->getAdmin()?->getPhone());

        $this->client->request('POST', 'https://pbx.reset-med.ru:8089/ari/channels', [
            'auth_basic' => 'ARI_user:G5heZ8ld03V1I3',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'endpoint' => 'Local/' . $adminPhone . '@from-inclient',
                'extension' => $clientPhone,
                'context' => 'from-inclient',
                'priority' => '1',
            ],
        ]);
    }
}
