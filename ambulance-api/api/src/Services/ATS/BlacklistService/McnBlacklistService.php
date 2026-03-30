<?php

declare(strict_types=1);

namespace App\Services\ATS\BlacklistService;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class McnBlacklistService
{
    private HttpClientInterface $client;

    public function __construct(
        HttpClientInterface $client,
        string $token
    ) {
        $this->client = $client->withOptions([
            'base_uri' => 'https://vpbx.mcn.ru/api/protected/',
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function addToBlacklist(string $phone): void
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phone);

        $phoneMember = $this->getPhoneMember($phoneNumber);

        if ($phoneMember) {
            return;
        }

        $this->client->request(
            'POST',
            'vpbx/blacklists/2846/members',
            [
                'headers' => [
                    'accept' => '*/*',
                    'Content-Type' => 'application/json',
                    'Sec-Fetch-Mode' => 'cors',
                ],
                'json' => ['number' => $phoneNumber],
            ]
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function deleteFromBlacklist(string $phone): void
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phone);

        $phoneMember = $this->getPhoneMember($phoneNumber);

        if (!$phoneMember) {
            return;
        }

        $this->client->request(
            'DELETE',
            'vpbx/blacklists/2846/members?blacklistMemberId=' . $phoneMember->id,
            [
                'headers' => [
                    'accept' => '*/*',
                    'Content-Type' => 'application/json',
                    'Sec-Fetch-Mode' => 'cors',
                ],
            ]
        );
    }

    /**
     * @param mixed $phoneNumber
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function getPhoneMember($phoneNumber): ?PhoneMember
    {
        $response = $this->client->request(
            'GET',
            'vpbx/blacklists/2846/members'
        );

        $res = $response->toArray(false);

        foreach ($res['data'] as $item) {
            if ($item['number'] === $phoneNumber) {
                return PhoneMember::fromArray($item);
            }
        }

        return null;
    }
}
