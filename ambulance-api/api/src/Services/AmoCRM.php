<?php

declare(strict_types=1);

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use App\Repository\AmoCrmTokenRepository;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AmoCRM
{
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCrmTokenRepository $tokens,
    ) {
        $apiClient = new AmoCRMApiClient(
            'd80b0f1f-1687-4b1e-8abd-9f3cbbe7a19e',
            'fCUzh7hiQ1bcuKQSrdJVp7Mnwnwi4b2vsK4W7yzhBCcumEkvRcHl3wX3hVxglhmK',
            'https://reset-med.ru/app/api/amo-crm/auth/callback'
        );

        $token = $tokens->getToken();

        $apiClient->setAccessToken($token)
            ->setAccountBaseDomain('af4040148.amocrm.ru')
            ->onAccessTokenRefresh(
                static function (AccessTokenInterface $accessToken, string $baseDomain) use ($tokens): void {
                    $tokens->update($accessToken, $baseDomain);
                }
            );

        $this->client = $apiClient;
    }

    public function getClient(): AmoCRMApiClient
    {
        return $this->client;
    }
}
