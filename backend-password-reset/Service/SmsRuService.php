<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Отправка SMS через SMS.ru API.
 *
 * Требуется переменная окружения SMSRU_API_ID.
 * @see https://sms.ru/api
 */
class SmsRuService
{
    private string $apiId;
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(
        string $smsRuApiId,
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
    ) {
        $this->apiId = $smsRuApiId;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function send(string $phone, string $message): bool
    {
        // Формат телефона: 79991234567 (11 цифр)
        $phone = preg_replace('/\D/', '', $phone);

        try {
            $response = $this->httpClient->request('GET', 'https://sms.ru/sms/send', [
                'query' => [
                    'api_id' => $this->apiId,
                    'to'     => $phone,
                    'msg'    => $message,
                    'json'   => 1,
                ],
            ]);

            $data = $response->toArray();

            if (($data['status'] ?? null) === 'OK') {
                $this->logger->info('SMS sent', ['phone' => $phone]);
                return true;
            }

            $this->logger->error('SMS.ru error', ['response' => $data]);
            return false;
        } catch (\Throwable $e) {
            $this->logger->error('SMS sending failed', [
                'phone'   => $phone,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }
}
