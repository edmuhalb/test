<?php

declare(strict_types=1);

namespace App\Services\YaGeolocation;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Api
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $token
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getPositionByAddress(string $address): ?Position
    {
        $response = $this->client->request(
            'GET',
            'https://geocode-maps.yandex.ru/1.x/',
            [
                'query' => [
                    'apikey' => $this->token,
                    'geocode' => $address,
                    'results' => 1,
                    'format' => 'json',
                ],
            ]
        );

        $data = $response->toArray(false);
        if (isset($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'])) {
            $positionString = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
            $cord = explode(' ', $positionString);
            return new Position($cord[0], $cord[1]);
        }
        return null;
    }
}
