<?php

declare(strict_types=1);

namespace App\Services\SmsSender;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class McnSmsSender
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
            'POST',
            'https://a2p-sms-api.mcn.ru/api/a2p_sms/api/v1.1/send_sms',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Sec-Fetch-Mode' => 'cors',
                    'Authorization' => 'Bearer 5fbaa4938bb36db16e357ba2079e9e2f5dfb7b3c4307a713',
                ],
                'json' => [
                    'title' => 'MCNtelecom',
                    'sender' => 'MCNtelecom',
                    'receiver' => $phone,
                    'msgdata' =>  $message,
                ],
            ]
        );
    }
}
