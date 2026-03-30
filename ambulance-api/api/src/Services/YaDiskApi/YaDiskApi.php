<?php

declare(strict_types=1);

namespace App\Services\YaDiskApi;

use Http\Discovery\Exception\NotFoundException;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class YaDiskApi implements YaDiskApiInterface
{
    private const URL = '';

    private HttpClientInterface $client;

    public function __construct(
        HttpClientInterface $client,
    ) {
        $this->client = $client->withOptions([
            // 'base_uri' => 'http://ewaym.beget.tech/bitrix/services/main/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'OAuth y0_AgAAAABadthsAAygZgAAAAEU3Qm9AACcpIw8WvhJM6d-NBv-i_fv9ljntA',
            ],
        ]);
    }

    public function upload($from, $to): void
    {
        $response = $this->client->request(
            'GET',
            "https://cloud-api.yandex.net/v1/disk/resources/upload?path={$to}&overwrite=true"
        );

        $result = $response->toArray(false);

        $formData = new FormDataPart([
            'file' => DataPart::fromPath($from),
        ]);

        $this->client->request(
            'PUT',
            $result['href'],
            [
                'body'=>$formData->bodyToIterable(),
            ]
        );
    }

    public function delete(string $path): void
    {
        $response = $this->client->request(
            'DELETE',
            'https://cloud-api.yandex.net/v1/disk/resources?path=disk:/Аудиозаписи вызовов/11&permanently=true'
        );

        if ($response->getStatusCode() === 404) {
            $result = $response->toArray(false);
            throw new NotFoundException($result['message']);
        }

        dd($response->getStatusCode());
    }
}
