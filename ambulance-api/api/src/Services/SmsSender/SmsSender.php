<?php

declare(strict_types=1);

namespace App\Services\SmsSender;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsSender
{
    public function __construct(
        readonly private HttpClientInterface $client,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function send(string $phone, string $message): void
    {
        $this->client->request(
            'GET',
            'https://sms.ru/sms/send?api_id=6F4B7B5D-AB04-3F68-8B13-AF80351F271E&to=' . $phone . '&msg=' . $message . '&json=1'
        );
    }
}
