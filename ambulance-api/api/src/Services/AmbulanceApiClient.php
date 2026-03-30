<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class AmbulanceApiClient
{
    public function __construct(
        private HttpClientInterface $client,
        private string $baseUrl,
        private string $username,
        private string $password,
    ) {}

    /**
     * @param string $endpoint Путь к эндпоинту (например, "calls" или "calls/stats/status")
     * @param array<string, mixed> $queryParams Параметры запроса
     * @param string $method HTTP метод (по умолчанию GET)
     * @param array<string, mixed>|null $body Тело запроса (для POST/PUT запросов)
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function request(
        string $endpoint,
        array $queryParams = [],
        string $method = 'GET',
        ?array $body = null
    ): ResponseInterface {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        // Фильтруем и нормализуем параметры
        $normalizedParams = [];
        foreach ($queryParams as $key => $value) {
            if ($value !== null && $value !== '') {
                $normalizedParams[$key] = (string) $value;
            }
        }

        // Собираем URL с query-параметрами вручную
        if (!empty($normalizedParams)) {
            $queryString = http_build_query($normalizedParams, '', '&', PHP_QUERY_RFC3986);
            $url .= '?' . $queryString;
        }

        $options = [
            'auth_basic' => [$this->username, $this->password],
        ];

        // Добавляем JSON body для POST/PUT запросов
        if ($body !== null && \in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            $options['json'] = $body;
            $options['headers'] = [
                'Content-Type' => 'application/json',
            ];
        }

        return $this->client->request(
            $method,
            $url,
            $options
        );
    }

    /**
     * @param string $endpoint Путь к эндпоинту
     * @param array<string, mixed> $queryParams Параметры запроса
     * @param string $method HTTP метод
     * @param array<string, mixed>|null $body Тело запроса (для POST/PUT запросов)
     * @return array{statusCode: int, data: mixed}
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function requestAndGetResponse(
        string $endpoint,
        array $queryParams = [],
        string $method = 'GET',
        ?array $body = null
    ): array {
        $response = $this->request($endpoint, $queryParams, $method, $body);
        $statusCode = $response->getStatusCode();

        try {
            $data = $response->toArray(false);
        } catch (DecodingExceptionInterface) {
            $data = $response->getContent(false);
        }

        return [
            'statusCode' => $statusCode,
            'data' => $data,
        ];
    }
}

