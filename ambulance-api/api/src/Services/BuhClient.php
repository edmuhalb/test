<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\ApiCallLog;
use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\ApiCallLogRepository;
use DateTimeImmutable;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class BuhClient
{
    public function __construct(
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
        private string $buhUsername,
        private string $buhPassword,
        private ApiCallLogRepository $logs,
        private Flusher $flusher,
    ) {}

    /**
     * @param Calling $call
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function send(Calling $call): void
    {

        $data = $this->serializer->normalize($call, null, [
            'groups' => [
                'exchange_calling:read',
                'partner:item:read',
                'user:item:read',
                'service:item:read',
                'client:item:read',
            ],
        ]);

        $response = $this->client->request(
            'POST',
            'https://f2df32f2-787f-4a0a-b0b2-f31d3dc32464.wc.ru-3.1c.selcloud.ru/umc_union/hs/calls/UploadCalls',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Sec-Fetch-Mode' => 'cors',
                ],
                'auth_basic' => [$this->buhUsername, $this->buhPassword],
                'json' => [
                    $data
                ],
            ]
        );

        $result = $response->toArray(false);
        $status = $response->getStatusCode();

        $log = new ApiCallLog();
        $log->setCreatedAt(new DateTimeImmutable());
        $log->setCallId($call->getId());
        $log->setData($data);
        $log->setResponseStatus($status);
        $log->setResponseData($result);
        $this->logs->add($log);
        $this->flusher->flush();
    }
}
